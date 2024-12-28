#print("hi")
import mysql.connector
import numpy as np
import pandas as pd



db = mysql.connector.connect(host="localhost",user="root",password="",database="crop")


cursor = db.cursor()

# SQL query to select n, p, k from tbl_data
select_query = "SELECT n, p, k FROM tbl_data order by id DESC limit 1"

# Execute the query
cursor.execute(select_query)

# Fetch all rows from the result
rows = cursor.fetchall()
# Assuming rows is a list of tuples like [(50, 20, 30)]
if rows:
    # Extract the first (latest) row
    n, p, k = rows[0]

    # Convert to DataFrame
    new_data = pd.DataFrame({'N': [n], 'P': [p], 'K': [k]})
    print(new_data)
else:
    print("No data found.")


# Step 1: Load the dataset
df = pd.read_csv("Crop_recommendation.csv")
print(df.head())  # Display the first few rows of the dataset

# Step 2: Check for missing values
print(df.isnull().sum())  # Check for any null values in the dataset

# Step 3: Define the feature variables (N, P, K) and the target variable (label)
x = df[['N', 'P', 'K']]
y = df['label']

# Step 4: Split the dataset into training and testing sets
from sklearn.model_selection import train_test_split
x_train, x_test, y_train, y_test = train_test_split(x, y, test_size=0.25)

# Step 5: Initialize the Decision Tree Classifier
from sklearn.tree import DecisionTreeClassifier
classifier = DecisionTreeClassifier()

# Step 6: Train the classifier on the training data
classifier.fit(x_train, y_train)

# Step 7: Make predictions on the test data
pred = classifier.predict(x_test)


# Step 8: Evaluate the accuracy of the model
from sklearn.metrics import accuracy_score
acc=accuracy_score(y_test, pred)  # Print the accuracy score
print(acc)
# Step 9: Predict the crop yield for new input data
##new_data = pd.DataFrame({'N': [50], 'P': [20], 'K': [30]})
##print(new_data)
predicted_yield = classifier.predict(new_data)
print(predicted_yield)  # Print the predicted crop yield

accstr=str(acc)
predictedop=str(predicted_yield)
predictedop= predictedop.strip("[]").strip("'")




insert_query = """
    INSERT INTO tbl_crop (crop, percentage)
    VALUES (%s, %s)
"""

# Data to insert
values = (predictedop,accstr)

try:
    # Execute the query
    cursor.execute(insert_query, values)

    # Commit the changes
    db.commit()

    print(f"Data inserted successfully: {cursor.rowcount} row(s) affected.")
except mysql.connector.Error as e:
    print(f"Error inserting data: {e}")
finally:
    # Close the cursor and database connection
    cursor.close()
    db.close()

cursor.close()
db.close()
