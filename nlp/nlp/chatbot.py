import spacy
import json
import sys
import psycopg2
from datetime import date, timedelta

# Load the SpaCy model
nlp = spacy.load("en_core_web_sm")

# Gender mapping (adjust to your DB values)
GENDER_MAP = {
    "male": 1,
    "female": 2,
    "other": 3
}


# Connect to your PostgreSQL DB
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
        print(f"Error connecting to database: {e}")
        return None

# Get date range based on filter (today, weekly, monthly, etc.)
def get_date_range(date_filter):
    today = date.today()
    if date_filter == "today":
        return today, today
    elif date_filter == "this_week":
        start = today - timedelta(days=today.weekday())  # Start of this week
        return start, today
    elif date_filter == "this_month":
        start = today.replace(day=1)  # First day of the current month
        return start, today
    elif date_filter == "last_month":
        start = (today.replace(day=1) - timedelta(days=1)).replace(day=1)  # First day of last month
        end = (today.replace(day=1) - timedelta(days=1))  # Last day of last month
        return start, end
    return today, today

# Fetch visit count for the given gender and date filter
def fetch_visit_count(gender, date_filter):
    gender_value = GENDER_MAP.get(gender)
    start_date, end_date = get_date_range(date_filter)

    # Make sure the gender_value is valid
    if gender_value is None:
        return f"Invalid gender: {gender}"

    # Establish DB connection and execute the query
    conn = get_db_connection()
    if conn is None:
        return "Error connecting to the database"

    try:
        cursor = conn.cursor()

        # Print debugging info
        print(f"Executing query for gender: {gender} (mapped to {gender_value}), date range: {start_date} to {end_date}")

        # Define the query with parameterized inputs to avoid SQL injection
        query = """
            SELECT SUM(counts) FROM counters
            WHERE gender = %s AND DATE(created_at) BETWEEN %s AND %s
        """
        cursor.execute(query, (gender_value, start_date, end_date))
        count = cursor.fetchone()[0]

        # Debugging: Print the count value to check if it was fetched correctly
        print(f"Count fetched: {count}")

        cursor.close()
        conn.close()

        # Return a more informative message if no count is found
        if count is None:
            return f"No visits recorded for {gender} between {start_date} and {end_date}."

        return count

    except psycopg2.Error as e:
        print(f"Error executing query: {e}")
        cursor.close()
        conn.close()
        return f"Error fetching visit count: {e}"

# Function to extract gender and date from user message using SpaCy
def extract_info_from_message(user_message):
    # Process the message with SpaCy
    doc = nlp(user_message.lower())

    # Default values
    gender = "other"
    date_filter = "today"

    # Check for gender mentions
    for ent in doc.ents:
        if ent.label_ == "PERSON":
            if "male" in ent.text:
                gender = "male"
            elif "female" in ent.text:
                gender = "female"

    # Check for date mentions (simplified example for "today", "this week", "this month")
    if "today" in user_message:
        date_filter = "today"
    elif "this week" in user_message:
        date_filter = "this_week"
    elif "this month" in user_message:
        date_filter = "this_month"
    elif "last month" in user_message:
        date_filter = "last_month"

    return gender, date_filter

# Main entry point: getting message from user and extracting info
if len(sys.argv) > 1:
    try:
        user_message = sys.argv[1]  # Receive message as input
        gender, date_filter = extract_info_from_message(user_message)  # Extract gender and date range
        visit_count = fetch_visit_count(gender, date_filter)
        print(json.dumps({"count": visit_count}))  # Return visit count as JSON
    except Exception as e:
        print(json.dumps({"error": str(e)}))
else:
    # For testing directly in terminal
    user_message = "What is the total male visits today?"  # Example message
    gender, date_filter = extract_info_from_message(user_message)
    visit_count = fetch_visit_count(gender, date_filter)
    print(f"Visit count for {gender} on {date_filter}: {visit_count}")
