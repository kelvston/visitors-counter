

import spacy
from spacy.training import Example
from spacy.training import offsets_to_biluo_tags

# Load a blank English model
nlp = spacy.blank("en")

# Create an empty NER component
ner = nlp.add_pipe("ner")

# Add labels to the NER component
ner.add_label("GENDER")
ner.add_label("DATE")

# Define your expanded training data
TRAINING_DATA = [
    ("What is the count of male visitors today?", [(20, 24, "GENDER"), (29, 33, "DATE")]),
    ("How many female visitors were there in June?", [(9, 24, "GENDER"), (30, 35, "DATE")]),
    ("The male attendees were numerous in July.", [(4, 8, "GENDER"), (36, 40, "DATE")]),
    ("Count the number of female participants in December.", [(20, 39, "GENDER"), (42, 51, "DATE")]),
    ("How many boys came to the event in March?", [(14, 18, "GENDER"), (32, 37, "DATE")]),
    ("The event had a high number of girls last November.", [(21, 27, "GENDER"), (37, 50, "DATE")]),
    ("Were there any male visitors in May?", [(20, 24, "GENDER"), (30, 33, "DATE")]),
    ("The count of boys in April was significant.", [(13, 17, "GENDER"), (27, 32, "DATE")]),
    ("Can you tell me about female visitors for the past week?", [(16, 22, "GENDER"), (29, 41, "DATE")]),
    ("Male and female attendees were present in January.", [(0, 4, "GENDER"), (9, 15, "GENDER"), (31, 38, "DATE")]),
    ("Total visits of male on June 11", [(18, 22, "GENDER"), (25, 31, "DATE")]),
    ("What was the number of female attendees on June 11?", [(28, 34, "GENDER"), (39, 45, "DATE")]),
    ("How many male visitors were there on March 15?", [(20, 24, "GENDER"), (30, 37, "DATE")]),
    ("Count the female participants for April 5.", [(6, 11, "GENDER"), (19, 24, "DATE")]),
    ("What is the attendance of girls on November 20?", [(12, 17, "GENDER"), (22, 29, "DATE")])
]


# Convert training data into SpaCy's format
def convert_to_spacy_format(training_data):
    converted_data = []
    for text, annotations in training_data:
        doc = nlp.make_doc(text)
        entities = []
        for start, end, label in annotations:
            span = doc.char_span(start, end, label=label, alignment_mode="expand")
            if span is not None:
                entities.append((span.start_char, span.end_char, label))
            else:
                print(f"Skipping overlapping entity: {text[start:end]} ({label})")
        converted_data.append((text, {"entities": entities}))
    return converted_data

TRAINING_DATA_CONVERTED = convert_to_spacy_format(TRAINING_DATA)

# Training loop
optimizer = nlp.begin_training()
for epoch in range(10):
    losses = {}
    for text, annotations in TRAINING_DATA_CONVERTED:
        doc = nlp.make_doc(text)
        example = Example.from_dict(doc, annotations)
        nlp.update([example], drop=0.5, losses=losses)
    print(f"Epoch {epoch}, Losses: {losses}")

# Validate alignment
for text, annotations in TRAINING_DATA_CONVERTED:
    doc = nlp.make_doc(text)
    biluo_tags = offsets_to_biluo_tags(doc, annotations['entities'])
    print(f"Text: '{text}'")
    print(f"Entities: {annotations['entities']}")
    print(f"BILUO Tags: {biluo_tags}")

# Save the trained model
nlp.to_disk("spacy_model_expanded")

# Load and test the model
print("Loading the model...")
nlp_loaded = spacy.load("spacy_model_expanded")

# Test the model
test_text = "There were many girls and boys visiting in September."
doc = nlp_loaded(test_text)
for ent in doc.ents:
    print(f"{ent.text} ({ent.label_})")
