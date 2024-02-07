from flask import Flask, request, jsonify
import os
import logging
from flask_cors import CORS
import openai
import mysql.connector

logging.basicConfig(level=logging.INFO)

app = Flask(__name__)
CORS(app, origins="*", methods=['GET', 'POST'], allow_headers=["Content-Type"])

index_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), "index.json")
api_key_file = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'api_key.txt')

openai.api_key = 'sk-.......'



def load_index_from_file():
    try:
        with open(index_path, 'r') as index_file:
            index_data = index_file.read()
        return index_data
    except Exception as e:
        logging.error(f"Error loading index from file: {e}")
        return None


@app.route('/ask', methods=['POST'])
def ask():
    try:
        user_question = request.json['text']

        index_data = load_index_from_file()
        if index_data:
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo",  # Используйте модель чата GPT-3.5-turbo или аналогичную
                messages=[
                    {"role": "system", "content": "You are a helpful assistant."},
                    {"role": "user", "content": index_data},
                    {"role": "user", "content": user_question}
                ],

            )

            return jsonify({'response': response.choices[0].message['content']})
        else:
            return jsonify({'response': 'Error: Failed to load index data'})
    except Exception as e:
        logging.error(f"Error processing request: {e}")
        return jsonify({'response': f'Error: {e}'})

if __name__ == "__main__":
    app.run(debug=True)


