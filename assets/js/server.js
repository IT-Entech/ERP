const express = require('express');
const sql = require('mssql');
const cors = require('cors');

const app = express();
app.use(cors());

// Database configuration
const dbConfig = {
    user: 'sa',
    password: 'System2560',
    server: '203.151.66.176',
    database: 'EntechDB',
    options: {
        encrypt: true, // For Azure SQL
        trustServerCertificate: true // Change to true for local dev / self-signed certs
    }
};

app.get('/api/sales', async (req, res) => {
    try {
        await sql.connect(dbConfig);
        const result = await sql.query(`
            SELECT A.staff_id, B.fname_e, B.nick_name 
            FROM xuser AS A
            LEFT JOIN hr_staff B ON A.staff_id = B.staff_id
            WHERE gid = '16387' 
              AND usrid NOT IN ('16387', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
                                '104', '105', '107', '110', '112', '115', '122', 124, 125, 126, 
                                128, 129, 131, 132, 133, 135, 140, 150) 
              AND isactive = 'Y' 
              AND A.staff_id <> ''
        `);
        res.json(result.recordset);
    } catch (err) {
        console.error(err);
        res.status(500).send('Server Error');
    }
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));

