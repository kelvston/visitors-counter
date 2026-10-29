# visitors-counter

## Chatbot NLP model

The chatbot uses a spaCy intent and entity model trained from `intent_data.json`,
`entity_data.json`, and additional examples saved by signed-in users.

Every signed-in user can open **Train Chatbot**, add labeled examples, and
retrain the model. The page requires authentication and is rate-limited. Added
examples are stored in `storage/app/chatbot-training.json`.

The CLI training command is also available:

```bash
python3 -m venv .venv
.venv/bin/python -m pip install -r nlp/requirements.txt
php artisan chatbot:train
```

On Windows, use `.venv\Scripts\python.exe` for the Python commands. The trained
model is written to `storage/app/chatbot-nlp-model` and is intentionally excluded
from version control; train it on each environment after installing dependencies.
Set `CHATBOT_PYTHON_BINARY` or `CHATBOT_NLP_MODEL_PATH` in the environment to
override the default runtime or model location.

Add natural-language examples to the `texts` array in `intent_data.json` and
matching annotations at the same index in `annotations`. Use the `greeting`,
`help`, `unsupported`, `total_visits_by_gender_in_month`, or
`total_visits_by_gender_in_period` intent labels. Annotate values such as
`gender`, `month`, and `period` in each example's `entities` object.