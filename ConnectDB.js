// db.js

// Import the mssql module
const sql = require('mssql');

// Configuration for your database connection
const config = {
    user: 'sa',                          // Your database username
    password: 'System2560',               // Your database password
    server: '203.151.66.176',             // Server name or IP address
    port: 55449,                          // Port number (if not the default 1433)
    database: 'EntechDB',                 // Your database name
    options: {
        encrypt: true,                    // Use encryption (required for Azure SQL databases)
        trustServerCertificate: true      // Disable SSL verification for local development
    }
};

async function connectToDB() {
    try {
        const pool = await sql.connect(config);
        return pool;
    } catch (error) {
        console.error('Database connection failed:', error);
        throw error;
    }
}

// Export the connection function
module.exports = connectToDB;
