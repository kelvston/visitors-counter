# import spacy
# import json
# import sys
# import os
# import logging
# import psycopg2
# from datetime import date, timedelta
# from spacy.matcher import Matcher
#
# # Setup logging
# logging.basicConfig(level=logging.INFO)
#
# # Load SpaCy model
# nlp = spacy.load("en_core_web_sm")
#
# # Gender mapping for database
# GENDER_MAP = {
#     "male": 1,
#     "female": 2
# }
#
# # Setup matcher with corrected patterns
# matcher = Matcher(nlp.vocab)
# # Correct pattern attribute to "LOWER" and ensure no typos
# matcher.add("MALE", [[{"LOWER": keyword}] for keyword in ["male", "boy", "boys", "man", "men"]])
# matcher.add("FEMALE", [[{"LOWER": keyword}] for keyword in ["female", "girl", "girls", "woman", "women"]])
#
# # Secure DB connection (unchanged)
# def get_db_connection():
#     try:
#         return psycopg2.connect(
#             host="127.0.0.1",
#             user="postgres",
#             password="Kelvin@2024",
#             dbname="vcs",
#             port="5432"
#         )
#     except psycopg2.Error as e:
#         logging.error(f"Error connecting to database: {e}")
#         return None
#
# # Date filters (unchanged)
# def get_date_range(date_filter):
#     today = date.today()
#     if date_filter == "today":
#         return today, today
#     elif date_filter == "this_week":
#         start = today - timedelta(days=today.weekday())
#         return start, today
#     elif date_filter == "this_month":
#         start = today.replace(day=1)
#         return start, today
#     elif date_filter == "last_month":
#         first_this_month = today.replace(day=1)
#         last_last_month = first_this_month - timedelta(days=1)
#         start = last_last_month.replace(day=1)
#         return start, last_last_month
#     return today, today  # fallback
#
# # Main query handler (unchanged)
# def fetch_visit_count(gender, date_filter):
#     gender_value = GENDER_MAP.get(gender)
#     start_date, end_date = get_date_range(date_filter)
#
#     if gender is None or start_date is None or end_date is None:
#         logging.error("Invalid input parameters.")
#         return "Invalid input parameters."
#
#     conn = get_db_connection()
#     if conn is None:
#         return "Database connection error."
#
#     try:
#         cursor = conn.cursor()
#         logging.info(f"Fetching visits for gender={gender}, dates={start_date} to {end_date}")
#
#         if gender_value is None:
#             query = "SELECT SUM(counts) FROM counters WHERE DATE(created_at) BETWEEN %s AND %s"
#             cursor.execute(query, (start_date, end_date))
#         else:
#             query = "SELECT SUM(counts) FROM counters WHERE gender = %s AND DATE(created_at) BETWEEN %s AND %s"
#             cursor.execute(query, (gender_value, start_date, end_date))
#
#         count = cursor.fetchone()[0]
#         cursor.close()
#         conn.close()
#
#         return count if count else 0
#
#     except psycopg2.Error as e:
#         logging.error(f"Database error: {e}")
#         return f"Database error: {e}"
#     except Exception as e:
#         logging.error(f"Unexpected error: {e}")
#         return f"An error occurred: {e}"
#
# # NLP processing with enhanced logging
# def extract_info_from_message(user_message):
#     doc = nlp(user_message.lower())
#     matches = matcher(doc)
#
#     gender = None
#     date_filter = "today"
#
#     for match_id, start, end in matches:
#         match_label = nlp.vocab.strings[match_id]
#         logging.info(f"Matched label: {match_label}")
#         if match_label == "MALE":
#             gender = "male"
#         elif match_label == "FEMALE":
#             gender = "female"
#
#     # Date filter detection
#     msg = user_message.lower()
#     if "today" in msg:
#         date_filter = "today"
#     elif "this week" in msg or "week" in msg:
#         date_filter = "this_week"
#     elif "this month" in msg:
#         date_filter = "this_month"
#     elif "last month" in msg:
#         date_filter = "last_month"
#
#     gender = gender or "all"  # Default to 'all' if no gender detected
#     logging.info(f"Extracted gender: {gender}, date_filter: {date_filter}")
#     return gender, date_filter
#
# # Command-line entry point
# if __name__ == "__main__":
#     if len(sys.argv) > 1:
#         user_message = sys.argv[1]
#         gender, date_filter = extract_info_from_message(user_message)
#         visit_count = fetch_visit_count(gender, date_filter)
#         readable_filter = date_filter.replace("_", " ")
#         reply = f"There were {visit_count} {gender} visits {readable_filter}." if visit_count != 0 else f"No {gender} visits recorded {readable_filter}."
#         print(json.dumps({"reply": reply}))
#     else:
#         # Test case
#         test_message = "How many girls visited this week?"
#         gender, date_filter = extract_info_from_message(test_message)
#         visit_count = fetch_visit_count(gender, date_filter)
#         print(f"Test result: {visit_count} visits for {gender} ({date_filter})")

import spacy
import json
import sys
import os
import logging
import psycopg2
from datetime import date, timedelta
from spacy.matcher import Matcher
from dateutil.relativedelta import relativedelta
import re

# Setup logging (you can keep this for debugging if needed)
# logging.basicConfig(level=logging.INFO)

# Load SpaCy model
nlp = spacy.load("en_core_web_sm")

# Gender mapping for database
GENDER_MAP = {
    "male": 1,
    "female": 2
}

# Setup matcher with corrected patterns
matcher = Matcher(nlp.vocab)
matcher.add("GREETING", [[{"LOWER": keyword}] for keyword in ["hello", "hi", "hey", "greetings", "good morning", "good afternoon", "good evening"]])
matcher.add("MALE", [[{"LOWER": keyword}] for keyword in ["male", "boy", "boys", "man", "men"]])
matcher.add("FEMALE", [[{"LOWER": keyword}] for keyword in ["female", "girl", "girls", "woman", "women"]])

# Secure DB connection (unchanged)
def get_db_connection():
    try:
        return psycopg2.connect(
            host="127.0.0.1",
            user="postgres",
            password="Kelvin@2024",
            dbname="vcs",
            port="5432"
        )
    except psycopg2.Error as e:
        logging.error(f"Error connecting to database: {e}")
        return None

# Date filters (modified)
def get_date_range(date_filter):
    today = date.today()
    if date_filter == "today":
        return today, today
    elif date_filter == "yesterday":
        yesterday = today - timedelta(days=1)
        return yesterday, yesterday
    elif date_filter == "this_week":
        start = today - timedelta(days=today.weekday())
        return start, today
    elif date_filter == "last_week":
        start = today - timedelta(days=today.weekday() + 7)
        end = today - timedelta(days=today.weekday() + 1)
        return start, end
    elif date_filter == "this_month":
        start = today.replace(day=1)
        return start, today
    elif date_filter == "last_month":
        first_this_month = today.replace(day=1)
        last_last_month = first_this_month - timedelta(days=1)
        start = last_last_month.replace(day=1)
        return start, last_last_month
    elif date_filter == "last_year":
        start = date(today.year - 1, 1, 1)
        end = date(today.year - 1, 12, 31)
        return start, end
    elif date_filter.startswith("months_ago_"):
        n_months = int(date_filter.split("_")[2])
        past_date = today - relativedelta(months=n_months)
        return past_date.replace(day=1), today
    elif date_filter == "weekend":
        # Assuming weekend is Saturday and Sunday in Dar es Salaam
        if today.weekday() < 5:  # Monday to Friday, get last weekend
            start_delta = timedelta(days=today.weekday() + 2)
            end_delta = timedelta(days=today.weekday() + 1)
        else:  # Saturday or Sunday, get current weekend
            start_delta = timedelta(days=today.weekday() - 5)
            end_delta = timedelta(days=today.weekday() - 6)
        start = today - start_delta
        end = today - end_delta
        return min(start, end), max(start, end) # Ensure correct order
    elif date_filter == "all_days":
        return None, None # Handle in fetch_visit_count
    elif date_filter.startswith("day_ago_"):
        n_days = int(date_filter.split("_")[2])
        past_date = today - timedelta(days=n_days)
        return past_date, past_date
    elif date_filter.startswith("months_"):
        n_months = int(date_filter.split("_")[1])
        past_date = today - relativedelta(months=n_months)
        return past_date.replace(day=1), today # Assuming up to the current date
    elif date_filter.startswith("year_"):
        n_years = int(date_filter.split("_")[1])
        past_year = today.year - n_years
        return date(past_year, 1, 1), date(past_year, 12, 31)
    return today, today  # fallback

# Main query handler (modified)
def fetch_visit_count(gender, date_filter):
    gender_value = GENDER_MAP.get(gender)
    start_date, end_date = get_date_range(date_filter)

    if gender is None:
        logging.error("Invalid input parameters.")
        return {"reply": "Invalid input parameters."}

    conn = get_db_connection()
    if conn is None:
        return {"reply": "Database connection error."}

    try:
        cursor = conn.cursor()
        # logging.info(f"Fetching visits for gender={gender}, dates={start_date} to {end_date}")

        query = "SELECT SUM(counts) FROM counters"
        params = ()
        date_clause = ""
        gender_clause = ""

        if start_date and end_date:
            date_clause = "DATE(created_at) BETWEEN %s AND %s"
            params = params + (start_date, end_date)
        elif start_date:
            date_clause = "DATE(created_at) >= %s"
            params = params + (start_date,)
        elif end_date:
            date_clause = "DATE(created_at) <= %s"
            params = params + (end_date,)

        if gender_value:
            gender_clause = "gender = %s"
            params = params + (gender_value,)

        conditions = []
        if date_clause:
            conditions.append(date_clause)
        if gender_clause:
            conditions.append(gender_clause)

        if conditions:
            query += " WHERE " + " AND ".join(conditions)

        cursor.execute(query, params)
        count = cursor.fetchone()[0]
        cursor.close()
        conn.close()

        readable_filter = date_filter.replace("_", " ").replace("ago", "ago").capitalize()
        if date_filter.startswith("months_ago_"):
            n_months = int(date_filter.split("_")[2])
            past_date = date.today() - relativedelta(months=n_months)
            readable_filter = f"from {past_date.strftime('%Y-%m-%01d')} to {date.today().strftime('%Y-%m-%d')}"
        elif date_filter.startswith("day_ago_"):
            n_days = int(date_filter.split("_")[2])
            past_date = date.today() - timedelta(days=n_days)
            readable_filter = past_date.strftime('%Y-%m-%d')
        elif date_filter.startswith("months_"):
            n_months = int(date_filter.split("_")[1])
            past_date = date.today() - relativedelta(months=n_months)
            readable_filter = f"starting from {past_date.strftime('%Y-%m-%01d')}"
        elif date_filter.startswith("year_"):
            n_years = int(date_filter.split("_")[1])
            past_year = date.today().year - n_years
            readable_filter = f"for the year {past_year}"
        elif date_filter == "all_days":
            readable_filter = "all days"
        elif date_filter == "weekend":
            readable_filter = "the last/current weekend"
        elif date_filter == "this_month":
            readable_filter = "this month"


        if count is not None:
            return {"reply": f"There were {count} {gender if gender != 'all' else 'visitors'} who visited {readable_filter}."}
        else:
            return {"reply": f"No {gender if gender != 'all' else ''} visits recorded for {readable_filter}."}

    except psycopg2.Error as e:
        logging.error(f"Database error: {e}")
        return {"reply": f"Database error: {e}"}
    except Exception as e:
        logging.error(f"Unexpected error: {e}")
        return {"reply": f"An error occurred: {e}"}

# NLP processing with enhanced logging (modified)
def extract_info_from_message(user_message):
    doc = nlp(user_message.lower())
    matches = matcher(doc)

    gender = None
    date_filter = "today"
    ask_boys = False
    ask_girls = False
    is_greeting = False

    for match_id, start, end in matches:
        match_label = nlp.vocab.strings[match_id]
        # logging.info(f"Matched label: {match_label}")
        if match_label == "MALE":
            gender = "male"
            ask_boys = True
        elif match_label == "FEMALE":
            gender = "female"
            ask_girls = True
        elif match_label == "GREETING":
            is_greeting = True

    msg = user_message.lower()

    # More robust greeting detection using regex
    greeting_pattern = r"^(hello|hi|hey|greetings|good morning|good afternoon|good evening)(?![a-zA-Z])"
    if re.match(greeting_pattern, msg):
        is_greeting = True
        msg = re.sub(greeting_pattern, '', msg).strip() # Remove greeting for further processing

    if "yesterday" in msg and not is_greeting:
        date_filter = "yesterday"
    elif "last week" in msg and not is_greeting:
        date_filter = "last_week"
    elif ("this week" in msg or "week" in msg and "this" in msg) and not ("last week" in msg) and not is_greeting:
        date_filter = "this_week"
    elif "last month" in msg and not is_greeting:
        date_filter = "last_month"
    elif ("this month" in msg or "month" in msg and "this" in msg) and not ("last month" in msg) and not is_greeting:
        date_filter = "this_month"
    elif "last year" in msg or "year" in msg and "last" in msg and not is_greeting:
        date_filter = "last_year"
    elif "month ago" in msg and not is_greeting:
        parts = msg.split()
        for i, part in enumerate(parts):
            if part.isdigit() and parts[i+1] in ["month", "months"] and parts[i+2] == "ago":
                date_filter = f"months_ago_{int(part)}"
                break
    elif "day ago" in msg and not is_greeting:
        parts = msg.split()
        for i, part in enumerate(parts):
            if part.isdigit() and parts[i+1] in ["day", "days"] and parts[i+2] == "ago":
                date_filter = f"day_ago_{int(part)}"
                break
    elif msg.startswith("last ") and "month" in msg and not is_greeting:
        parts = msg.split()
        try:
            num_months = int(parts[1])
            date_filter = f"months_{num_months}"
        except ValueError:
            pass # Already handled "last month"
    elif msg.startswith("last ") and "year" in msg and not is_greeting:
        parts = msg.split()
        try:
            num_years = int(parts[1])
            date_filter = f"year_{num_years}"
        except ValueError:
            pass # Already handled "last year"
    elif "weekend" in msg and not is_greeting:
        date_filter = "weekend"
    elif ("all days" in msg or "all time" in msg or "ever" in msg) and not is_greeting:
        date_filter = "all_days"
    elif "today" in msg and not is_greeting:
        date_filter = "today" # Keep this last for more specific matches

    if is_greeting:
        return "greeting", "today", False, False
    elif "how many girls and boys" in msg and not is_greeting:
        return "all", date_filter, True, True
    elif ask_girls and not ask_boys and not is_greeting:
        return "female", date_filter, True, False
    elif ask_boys and not ask_girls and not is_greeting:
        return "male", date_filter, False, True
    else:
        return "all", date_filter, False, False # Default to all if no specific gender

if __name__ == "__main__":
    input_data = json.loads(sys.stdin.read())
    user_message = input_data.get('message')

    if user_message:
        response_gender, response_date_filter, ask_girls, ask_boys = extract_info_from_message(user_message)
        if response_gender == "greeting":
            print(json.dumps({"reply": "Hello! How can I help you today? Do you want to know visit statistics?"}))
        elif ask_girls and ask_boys:
            result_girls = fetch_visit_count("female", response_date_filter)
            result_boys = fetch_visit_count("male", response_date_filter)
            reply = f"There were {result_girls['reply'].split(' ')[2]} girls and {result_boys['reply'].split(' ')[2]} boys who visited {response_date_filter.replace('_', ' ').capitalize()}."
            print(json.dumps({"reply": reply}))
        elif ask_girls:
            result = fetch_visit_count("female", response_date_filter)
            print(json.dumps(result))
        elif ask_boys:
            result = fetch_visit_count("male", response_date_filter)
            print(json.dumps(result))
        else:
            result = fetch_visit_count(response_gender, response_date_filter)
            print(json.dumps(result))
        sys.stdout.flush()
    else:
        print(json.dumps({"reply": "No message received from chatbot."}))
        sys.stdout.flush()
# Command-line entry point for chatbot interaction
#
# if __name__ == "__main__":
#     user_query = "How many girls visited this week?"  # You can replace this with input() to make it interactive
#
#     gender, date_filter = extract_info_from_message(user_query)
#     visit_count = fetch_visit_count(gender, date_filter)
#     readable_filter = date_filter.replace("_", " ")
#
#     if gender == "female":
#         reply = f"There were {visit_count} girls who visited {readable_filter}."
#     elif gender == "male":
#         reply = f"There were {visit_count} boys who visited {readable_filter}."
#     elif gender == "all":
#         reply = f"There were {visit_count} visitors {readable_filter}."
#     else:
#         reply = "Could not determine the gender."
#
#     print(reply)
