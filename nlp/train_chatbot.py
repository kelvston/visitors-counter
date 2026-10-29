import argparse
import json
import os
import random
from pathlib import Path

import spacy
from spacy.training import Example


ROOT = Path(__file__).resolve().parent.parent
DEFAULT_OUTPUT = ROOT / "storage" / "app" / "chatbot-nlp-model"


def find_entity_spans(text, entities):
    spans = []
    lowered_text = text.casefold()

    for label, value in entities.items():
        value = str(value)
        start = lowered_text.find(value.casefold())
        if start >= 0:
            spans.append((start, start + len(value), label.upper()))

    return spans


def load_training_examples():
    with (ROOT / "intent_data.json").open(encoding="utf-8") as file:
        intent_data = json.load(file)
    with (ROOT / "entity_data.json").open(encoding="utf-8") as file:
        entity_data = json.load(file)

    if len(intent_data["texts"]) != len(intent_data["annotations"]):
        raise ValueError("intent_data.json must have one annotation for each text.")

    examples = []
    additional_data_path = Path(
        os.environ.get(
            "CHATBOT_TRAINING_DATA_PATH",
            ROOT / "storage" / "app" / "chatbot-training.json",
        )
    )
    if additional_data_path.exists():
        with additional_data_path.open(encoding="utf-8") as file:
            additional_data = json.load(file)
        intent_data["texts"].extend(additional_data.get("texts", []))
        intent_data["annotations"].extend(additional_data.get("annotations", []))

    if len(intent_data["texts"]) != len(intent_data["annotations"]):
        raise ValueError("Training data must have one annotation for each text.")

    for text, annotation in zip(intent_data["texts"], intent_data["annotations"]):
        spans = find_entity_spans(text, annotation.get("entities", {}))
        examples.append((text, annotation["intent"], spans))

    for item in entity_data:
        spans = [
            (entity["start"], entity["end"], entity["label"].upper())
            for entity in item.get("ents", [])
        ]
        examples.append((item["text"], "unsupported", spans))

    if not examples:
        raise ValueError("No chatbot training examples were found.")

    return examples


def train(output_path):
    examples = load_training_examples()
    labels = sorted({intent for _, intent, _ in examples})
    nlp = spacy.blank("en")
    textcat = nlp.add_pipe("textcat")
    for label in labels:
        textcat.add_label(label)

    ner = nlp.add_pipe("ner")
    for _, _, spans in examples:
        for _, _, label in spans:
            ner.add_label(label)

    training_examples = []
    for text, intent, spans in examples:
        entities = []
        for start, end, label in spans:
            span = nlp.make_doc(text).char_span(start, end, label=label)
            if span is not None:
                entities.append((span.start_char, span.end_char, label))

        training_examples.append(
            Example.from_dict(
                nlp.make_doc(text),
                {
                    "cats": {label: label == intent for label in labels},
                    "entities": entities,
                },
            )
        )

    optimizer = nlp.initialize(lambda: training_examples)
    random.seed(0)
    for epoch in range(60):
        random.shuffle(training_examples)
        losses = {}
        nlp.update(training_examples, sgd=optimizer, drop=0.2, losses=losses)
        if (epoch + 1) % 10 == 0:
            print(f"Epoch {epoch + 1}/60 - losses: {losses}")

    output_path.mkdir(parents=True, exist_ok=True)
    nlp.to_disk(output_path)
    print(f"Saved chatbot NLP model to {output_path}")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Train the chatbot NLP model.")
    parser.add_argument(
        "--output",
        type=Path,
        default=Path(os.environ.get("CHATBOT_NLP_MODEL_PATH", DEFAULT_OUTPUT)),
        help="Directory to save the trained spaCy model.",
    )
    train(parser.parse_args().output.resolve())
