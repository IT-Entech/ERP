var config=require('./dbConfig');
const sql=require('msnodesqlv8');

async function getLoginDetails(){
 try {
  sql.query(config,`
            SELECT A.staff_id, B.fname_e, B.nick_name 
            FROM xuser AS A
            LEFT JOIN hr_staff B ON A.staff_id = B.staff_id
            WHERE gid = '16387' 
              AND usrid NOT IN ('16387', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
                                '104', '105', '107', '110', '112', '115', '122', 124, 125, 126, 
                                128, 129, 131, 132, 133, 135, 140, 150) 
              AND isactive = 'Y' 
              AND A.staff_id <> ''
        `,(err,rows)=>{
   console.log(rows);
  })
 } catch (error ) {
  console.log(error);
 }
}
module.exports={
 getLoginDetails:getLoginDetails
}