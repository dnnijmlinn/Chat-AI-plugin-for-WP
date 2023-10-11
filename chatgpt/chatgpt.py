from flask import Flask, request, jsonify
from llama_index import SimpleDirectoryReader, GPTListIndex, readers, GPTSimpleVectorIndex, LLMPredictor, PromptHelper, ServiceContext
from langchain import OpenAI
import mysql.connector
import os
import time
import logging
from flask_cors import CORS
import phpserialize

logging.basicConfig(level=logging.INFO)

app = Flask(__name__)
CORS(app, origins="*", methods=['GET', 'POST'], allow_headers=["Content-Type"])

current_directory = os.path.dirname(os.path.abspath(__file__))
index_path = os.path.join(current_directory, "index.json")

base_dir = os.path.dirname(os.path.abspath(__file__))
api_key_file = os.path.join(base_dir, '..', 'api_key.txt')


@app.errorhandler(429)
def rate_limit_exceeded(e):
    return jsonify(error="rate limit exceeded"), 429


class Document:
    def __init__(self, doc_id, content, extra_info_str=None, embedding=None, extra_info=None):
        self.doc_id = doc_id
        self.content = content
        self.extra_info_str = extra_info_str
        self.embedding = embedding
        self.extra_info = extra_info 

    def get_doc_id(self):
        return self.doc_id

    def get_doc_hash(self):
        return hash(self.content)

    def get_text(self):
        return self.content

def construct_index_from_db():
    max_input_size = 4096
    num_outputs = 4096

    llm_predictor = LLMPredictor(llm=OpenAI(temperature=0.5, model_name="text-davinci-003", max_tokens=num_outputs))

    db = mysql.connector.connect(
        unix_socket='/your/path/to/unix/socket',
        user='user_name',
        password='users_password',
        database='database_name'
    )
    cursor = db.cursor()

    query = "SELECT option_value FROM wp_options WHERE option_name = 'materials_options'"
    cursor.execute(query)

    raw_data = cursor.fetchone()[0]
    deserialized_data = phpserialize.loads(raw_data.encode('utf-8'))
    text_data = deserialized_data[b'materials_setting'].decode('utf-8')

    # print(text_data)  

    documents = [Document(0, text_data)] 

    # print(documents[0].get_text()) 

    service_context = ServiceContext.from_defaults(llm_predictor=llm_predictor)
    index = GPTSimpleVectorIndex.from_documents(documents, service_context=service_context)

    index.save_to_disk(index_path)

    return index


@app.route('/ask', methods=['POST'])
def ask():
    index = GPTSimpleVectorIndex.load_from_disk(index_path)
    user_text = request.json['text']
    response = index.query(user_text)
    return jsonify({'response': response.response})

if __name__ == "__main__":
    with open(api_key_file, 'r') as file:
        gpt_api_key = file.read().strip()

    os.environ["OPENAI_API_KEY"] = gpt_api_key
    construct_index_from_db()
    app.run(debug=True)
