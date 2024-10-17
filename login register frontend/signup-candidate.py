import sqlite3
import bcrypt  # You can install this with 'pip install bcrypt'

# Function to hash the password
def hash_password(password):
    # Generate a salt and hash the password
    salt = bcrypt.gensalt()
    hashed = bcrypt.hashpw(password.encode('utf-8'), salt)
    return hashed

# Function to execute the SQL query
def execute_sql(sql, params):
    # Connect to the database (change this to your MySQL database if needed)
    conn = sqlite3.connect('voting_system.db')  # Replace this with MySQL connection if required
    cursor = conn.cursor()

    try:
        cursor.execute(sql, params)
        conn.commit()
        print("Account successfully created!")
    except sqlite3.Error as e:
        print(f"Error: {e}")
    finally:
        cursor.close()
        conn.close()

# Function to handle the signup process
def signup(full_name, email, password, aadhar_number, mobile_number, address, symbol_image):
    # Hash the password before storing
    hashed_password = hash_password(password)
    
    # SQL query to insert the data into the 'candidates' table
    sql = '''
    INSERT INTO candidates (full_name, email, password, aadharno, mobile, address, symbol_image)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    '''

    # Execute the SQL query with the user inputs
    execute_sql(sql, (full_name, email, hashed_password, aadhar_number, mobile_number, address, symbol_image))

# Example usage
full_name = "John Doe"
email = "johndoe@gmail.com"
password = "password123"
aadhar_number = "123412341234"
mobile_number = "9876543210"
address = "123 Main St, City"
symbol_image = "path/to/symbol_image.jpg"  # Image file path

# Call the signup function with the user data
signup(full_name, email, password, aadhar_number, mobile_number, address, symbol_image)
