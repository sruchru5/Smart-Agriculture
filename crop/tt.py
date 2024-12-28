import mysql.connector
from mysql.connector import Error

try:
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",  # Replace with your MySQL password if set
        database="crop"
    )
    cursor = db.cursor()
    print("Database connection successful")
except Error as e:
    print("Error:", e)
