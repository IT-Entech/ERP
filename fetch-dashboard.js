// fetchDashboard.js
const express = require('express');
const sql = require('mssql'); // Import the mssql module
const connectToDB = require('./ConnectDB'); // Import your database connection function
const router = express.Router();

// Endpoint to fetch dashboard data
router.get('/fetch-dashboard', async (req, res) => {
    const { year_no, month_no, channel, Sales, is_new } = req.query;

    try {
        const pool = await connectToDB(); // Get the connection pool

        // Your SQL query can include filters based on the parameters received
        const result = await pool.request()
            .input('year_no', sql.Int, year_no)
            .input('month_no', sql.Int, month_no)
            .input('channel', sql.VarChar, channel)
            .input('Sales', sql.VarChar, Sales)
            .input('is_new', sql.VarChar, is_new)
            .query('SELECT * FROM your_table WHERE year = @year_no AND month = @month_no AND channel = @channel AND Sales = @Sales AND is_new = @is_new'); // Adjust the query as needed

        // Send back the results
        res.json(result.recordset);
        pool.close();
    } catch (err) {
        console.error('Error fetching dashboard data:', err);
        res.status(500).json({ error: 'Server error' }); // Send error response
    }
});

module.exports = router;
