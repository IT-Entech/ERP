const nodemon = require('nodemon');
const dboperations = require('./dbOperations');

dboperations.getLoginDetails()
  .then(result => {
    console.log(result);
  })
  .catch(error => {
    console.error('Error fetching login details:', error);
  });
