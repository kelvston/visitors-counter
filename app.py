from http.server import BaseHTTPRequestHandler, HTTPServer
import json
import spacy
import dateparser

# Load the SpaCy model
# nlp = spacy.load('spacy_model')  # Replace with your model if different
nlp = spacy.load('spacy_model_expanded')  # Replace with your model if differentspacy_model_expanded

class RequestHandler(BaseHTTPRequestHandler):
    def _send_response(self, data, status_code=200):
        self.send_response(status_code)
        self.send_header('Content-type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(data).encode('utf-8'))

    def do_POST(self):
        if self.path == '/analyze':
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length).decode('utf-8')
            data = json.loads(post_data)
            text = data.get('text', '')

            # Process the text with SpaCy
            doc = nlp(text)

            # Prepare the response
            response = {
                'entities': [(ent.text, ent.label_) for ent in doc.ents],
                'tokens': [token.text for token in doc]
            }

            self._send_response(response)

        elif self.path == '/parse_date':
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length).decode('utf-8')
            data = json.loads(post_data)
            date_str = data.get('date_str', '')

            # Parse the date using dateparser
            parsed_date = dateparser.parse(date_str)
            if parsed_date:
                response = parsed_date.strftime('%Y-%m-%d')
            else:
                response = 'Invalid date'

            self._send_response({'date': response})

        else:
            self._send_response({'error': 'Not found'}, status_code=404)

def run(server_class=HTTPServer, handler_class=RequestHandler, port=8080):
    server_address = ('', port)
    httpd = server_class(server_address, handler_class)
    print(f'Starting httpd server on port {port}...')
    httpd.serve_forever()

if __name__ == '__main__':
    run()
