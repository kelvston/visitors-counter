import spacy
import json

def load_new_data(filename="new_data.json"):
    new_data = []
    try:
        with open(filename, "r") as f:
            for line in f:
                line = line.strip()
                if line:  # Check if the line is not empty
                    try:
                        entry = json.loads(line)
                        new_data.append(entry)
                    except json.JSONDecodeError as e:
                        print(f"Error decoding JSON in line: {line}. Error: {e}")
    except FileNotFoundError:
        pass  # If the file does not exist, we simply return an empty list

    return new_data

def is_query_in_new_data(query, new_data):
    for entry in new_data:
        if entry['text'] == query:
            return True
    return False

def check_and_append_query(query, model_path="spacy_model", new_data_file="new_data.json"):
    # Load the existing SpaCy model
    nlp = spacy.load(model_path)

    # Process the query with the current model
    doc = nlp(query)
    entities = [(ent.text, ent.label_) for ent in doc.ents]

    print(f"Query: {query}")
    print(f"Entities: {entities}")

    # Load existing new data
    new_data = load_new_data(new_data_file)

    # Check if the query is recognized or already in the new data
    if not entities and not is_query_in_new_data(query, new_data):
        print("Query not recognized by the model. Appending to new data.")
        new_entry = {
            "text": query,
            "entities": []  # You need to add actual entities manually or through some other mechanism
        }

        # Append the new data to the new_data.json file
        with open(new_data_file, "a") as f:
            f.write(json.dumps(new_entry) + "\n")

        print("New query appended to new_data.json")
    elif is_query_in_new_data(query, new_data):
        print("Query already exists in new_data.json.")
    else:
        print("Query recognized by the model.")

# Example usage
queries = [
    "what is male visitors today",
    "total visits last week",
    "count male visitors of June 22",
    "total visits of June",
    "total female visits in the last three months",
    "how many female visitors today",
    "visits of male in the last month",
    "how many visits last month",
    "total male visits of May",
    "female visits in April",
    "total visits of boys in June",
    "total visits of girls last week",
    "how many girls visited today",
    "count of boys in the last month",
    "what is time??"  # New query
]

for query in queries:
    check_and_append_query(query)
