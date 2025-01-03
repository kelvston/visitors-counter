import json

def store_new_data(text, entities):
    new_data = (text, {"entities": entities})
    with open('new_training_data.json', 'a') as f:
        f.write(json.dumps(new_data) + '\n')

# Example usage
if __name__ == "__main__":
    store_new_data("example text", [(0, 7, "ENTITY_TYPE")])
