function submitForm() {
    // Get input values
    const input1 = document.getElementById('input1').value;
    const input2 = document.getElementById('input2').value;
    const input3 = document.getElementById('input3').value;
  
    // Display inputs directly (XSS vulnerability)
    let resultHtml = '';
    if (input1) {
      resultHtml += `<p>XSS Testi: ${input1}</p>`; // XSS vulnerability
    }
    if (input2) {
      resultHtml += `<p>XSS Testi: ${escapeHtml(input2)}</p>`; // Sanitized
    }
    if (input3) {
      resultHtml += `<p>XSS Testi: ${escapeHtml(input3)}</p>`; // Sanitized
    }
  
    // Display entered values
    resultHtml += `
      <h3>Girdikleriniz:</h3>
      <p>Input 1: ${escapeHtml(input1)}</p>
      <p>Input 2: ${escapeHtml(input2)}</p>
      <p>Input 3: ${escapeHtml(input3)}</p>
    `;
  
    // Update result div
    document.getElementById('result').innerHTML = resultHtml;
  }
  
  // Function to escape HTML entities
  function escapeHtml(text) {
    return text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }
  