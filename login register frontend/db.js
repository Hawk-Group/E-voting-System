const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('aadhar.db');

// Initialize the Aadhar table with dummy data
db.serialize(() => {
  db.run(`CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    aadhar_number TEXT UNIQUE,
    phone_number TEXT,
    name TEXT
  )`);

  const stmt = db.prepare("INSERT OR IGNORE INTO users (aadhar_number, phone_number, name) VALUES (?, ?, ?)");
  stmt.run("123412341234", "9876543210", "John Doe");
  stmt.run("234523452345", "8765432109", "Jane Smith");
  stmt.run("345634563456", "7654321098", "Alice Johnson");
  stmt.finalize();
});

module.exports = db;
