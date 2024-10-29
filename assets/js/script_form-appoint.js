
    document.addEventListener('DOMContentLoaded', (event) => {
      fetch('./form-appoint.php')
          .then(response => {
              if (!response.ok) {
                  throw new Error(`HTTP error! Status: ${response.status}`);
              }
              return response.json();
          })
          .then(data => {
              const selectElement = document.getElementById('inputSales');
              const selectElement1 = document.getElementById('inputChannel');
              const selectElement2 = document.getElementById('inputSocial');
              const selectElement3 = document.getElementById('inputContract');
              const selectElement4 = document.getElementById('inputFac-nation');
              const selectElement5 = document.getElementById('inputProvince');
              const selectElement6 = document.getElementById('inputSegment');
              const selectElement7 = document.getElementById('inputCL_type');
              const selectElement8 = document.getElementById('inputAppoint');
              const selectElement9 = document.getElementById('inputCus_status');
              const selectElement10 = document.getElementById('inputis_status');
              data.sales_data.forEach(item => {
                  const option = document.createElement('option');
                  option.value = item.staff_id;
                  option.textContent = item.fname_e || item.nick_name || item.staff_id; 
                  selectElement.appendChild(option);
              });
              data.channel.forEach(item => {
                const option = document.createElement('option');
                option.value = item.sales_channels_group_code;
                option.textContent = item.sales_channels_group_name; 
                selectElement1.appendChild(option);
            });
            data.search.forEach(item => {
                const option = document.createElement('option');
                option.value = item.sales_channels_search_code;
                option.textContent = item.sales_channels_search_name; 
                selectElement2.appendChild(option);
            });
            data.contact.forEach(item => {
                const option = document.createElement('option');
                option.value = item.sales_channels_code;
                option.textContent = item.sales_channels_name; 
                selectElement3.appendChild(option);
            });
            data.nationality.forEach(item => {
                const option = document.createElement('option');
                option.value = item.nationality_code;
                option.textContent = item.nationality_name; 
                selectElement4.appendChild(option);
            });
            data.province.forEach(item => {
                const option = document.createElement('option');
                option.value = item.province_code;
                option.textContent = item.province_name; 
                selectElement5.appendChild(option);
            });
            data.segment.forEach(item => {
                const option = document.createElement('option');
                option.value = item.customer_segment_code;
                option.textContent = item.customer_segment_name; 
                selectElement6.appendChild(option);
            });
            data.CL.forEach(item => {
                const option = document.createElement('option');
                option.value = item.cleaning_type_code;
                option.textContent = item.cleaning_type_name; 
                selectElement7.appendChild(option);
            });
              // Get the selected value of the inputSegment dropdown
            const customerSegmentCode = selectElement6.value;

            // Disable selectElement7 if customerSegmentCode is not '04' or '05'
            if (customerSegmentCode !== '04' || customerSegmentCode !== '05') {
                selectElement7.disabled = true;
            }

            // Listen for changes in the inputSegment dropdown to dynamically enable/disable selectElement7
            selectElement6.addEventListener('change', () => {
                const updatedSegmentCode = selectElement6.value;
                selectElement7.disabled = updatedSegmentCode !== '04' && updatedSegmentCode !== '05';
            });
            data.is_appoint.forEach(item => {
                const option = document.createElement('option');
                option.value = item.is_appoint_code;
                option.textContent = item.is_appoint_name; 
                selectElement8.appendChild(option);
            });
            data.is_prospect.forEach(item => {
                const option = document.createElement('option');
                option.value = item.prospect_code;
                option.textContent = item.prospect_name; 
                selectElement9.appendChild(option);
            });
            data.appoint_status.forEach(item => {
                const option = document.createElement('option');
                option.value = item.status_code;
                option.textContent = item.status_name; 
                selectElement10.appendChild(option);
            });
          })
          .catch(error => console.error('Error fetching data:', error));
  });


