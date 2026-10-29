import json
import os
import sys
from pathlib import Path

import spacy


def main():
    default_model_path = (
        Path(__file__).resolve().parents[2]
        / "storage"
        / "app"
        / "chatbot-nlp-model"
    )
    model_path = Path(
        os.environ.get("CHATBOT_NLP_MODEL_PATH", default_model_path)
    )
    payload = json.loads(sys.stdin.read())
    message = payload.get("message", "")

    if not model_path.is_dir():
        raise FileNotFoundError(
            f"Chatbot model not found at {model_path}. "
            "Train it with nlp/train_chatbot.py."
        )

    nlp = spacy.load(model_path)
    doc = nlp(message)
    intent, confidence = max(doc.cats.items(), key=lambda item: item[1])

    print(
        json.dumps(
            {
                "intent": intent if confidence >= 0.35 else "unsupported",
                "confidence": confidence,
                "entities": [
                    {"text": entity.text, "label": entity.label_}
                    for entity in doc.ents
                ],
            }
        )
    )


if __name__ == "__main__":
    try:
        main()
    except Exception as error:
        print(json.dumps({"error": str(error)}))
        sys.exit(1)
