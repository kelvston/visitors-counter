import spacy
from spacy.training import Example
import json
import random

# Function to load training data from a file
def load_training_data(file_path):
    with open(file_path, 'r') as f:
        data = json.load(f)
    return [(item['text'], {'entities': item['entities']}) for item in data]

# Load your existing and new training data
existing_data = load_training_data('training_data.json')
new_data = load_training_data('new_data.json')

# Combine both datasets
TRAINING_DATA = existing_data + new_data

# Load the base model
nlp = spacy.load("en_core_web_sm")  # or another model if you're using one

# Add the NER component if it's not already there
if "ner" not in nlp.pipe_names:
    ner = nlp.create_pipe("ner")
    nlp.add_pipe(ner, last=True)

# Add the labels to the NER component
ner = nlp.get_pipe("ner")
for _, annotations in TRAINING_DATA:
    for ent in annotations.get("entities"):
        ner.add_label(ent[2])

# Alignment Check
print("Checking alignment of training data with entities...")
for text, annotations in TRAINING_DATA:
    doc = nlp.make_doc(text)
    biluo_tags = spacy.training.offsets_to_biluo_tags(doc, annotations['entities'])
    print(f"Text: {text}")
    print(f"Entities: {annotations['entities']}")
    print(f"BILUO Tags: {biluo_tags}\n")

# Training the model
optimizer = nlp.create_optimizer()
random.shuffle(TRAINING_DATA)  # Shuffle training data

for epoch in range(10):
    losses = {}
    for text, annotations in TRAINING_DATA:
        doc = nlp.make_doc(text)
        example = Example.from_dict(doc, annotations)
        nlp.update([example], drop=0.5, losses=losses)
    print(f"Epoch {epoch}, Losses: {losses}")

# Save the trained model
nlp.to_disk("spacy_model")
print("Model saved to 'spacy_model'")
