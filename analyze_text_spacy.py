import spacy
import json

# Load the SpaCy model
nlp = spacy.load("en_core_web_sm")

def analyze_text(text):
    doc = nlp(text)
    intent = recognize_intent(doc)  # Your logic for intent recognition
    entities = extract_entities(doc)  # Your logic for entity extraction

    return {
        "intent": intent,
        "entities": entities
    }

def recognize_intent(doc):
    # Basic logic for intent recognition
    # Example logic for demo purposes
    if any(token.text.lower() in ["weather", "temperature", "forecast"] for token in doc):
        return "weather"
    elif any(token.text.lower() in ["visitor", "count", "data"] for token in doc):
        return "visitor"
    elif any(token.text.lower() in ["male", "female", "gender"] for token in doc):
        return "gender"
    else:
        return "unknown"

def extract_entities(doc):
    return [(ent.text, ent.label_) for ent in doc.ents]

# Example usage
if __name__ == "__main__":
    import sys
    input_text = sys.argv[1]
    result = analyze_text(input_text)
    print(json.dumps(result))
