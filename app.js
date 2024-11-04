// server.js
const express = require('express');
const sql = require('mssql'); // Import the mssql module
const connectToDB = require('./connectDB'); // Your connection module
const app = express();
const port = 3000;

// Middleware to handle JSON requests
app.use(express.json());

// Endpoint to fetch staff members based on user level and role
app.get('/staff_id', async (req, res) => {
    const level = req.query.level; // Get level from query params
    const role = req.query.role; // Get role from query params

    // Debugging: Show incoming parameters
    console.log(`Level: ${level}, Role: ${role}`);

    let usrid;

    // Determine user IDs based on the level and role
    if (level === '3') {
        usrid = ['16387', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
            '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
            '127', '128', '129', '131', '132', '133', '135', '140', '150'];
    } else if ((level === '2' && (role === 'MK' || role === 'SUPER ADMIN'))) {
        usrid = ['16387', '23', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
            '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
            '127', '128', '129', '131', '132', '133', '135', '140', '150'];
    } else if (level === '2' && role === 'MK Online') {
        usrid = ['16387', '23', '25', '26', '36', '42', '47', '50', '79', '80', '89', 
            '96', '97', '101', '104', '105', '107', '110', '112', '115', '122', 
            '124', '125', '126', '127', '128', '129', '131', '132', '133', '135', 
            '140', '150'];
    } else if (level === '2' && role === 'MK Offline') {
        usrid = ['16387', '23', '25', '26', '30', '36', '42', '47', '50', '79', 
            '80', '89', '93', '96', '97', '101', '104', '105', '107', '110', 
            '112', '115', '118', '122', '124', '125', '126', '127', '128', 
            '129', '131', '132', '133', '135', '137', '138', '140', '150', '152'];
    }

    try {
        const pool = await connectToDB(); // Get the connection pool

        // Prepare SQL placeholders
        const placeholders = usrid.map(() => '?').join(',');
        const sqlQuery = `
            SELECT A.staff_id, B.fname_e, B.nick_name 
            FROM xuser AS A
            LEFT JOIN hr_staff B ON A.staff_id = B.staff_id
            WHERE gid = '16387' 
            AND usrid NOT IN (${placeholders})
            AND isactive = 'Y' 
            AND A.staff_id <> ''
        `;

        // Execute the query
        const result = await pool.request()
            .input('usrid', sql.VarChar(sql.MAX), usrid)
            .query(sqlQuery);

        const salesData = result.recordset;

        // Send JSON response
        res.json(salesData);
    } catch (error) {
        console.error('Database error:', error);
        res.status(500).json({ error: 'Internal Server Error' });
    }
});

// Start the server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
