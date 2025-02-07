// review and ratings

document.getElementById("reviewForm1").addEventListener("submit", function(e) {
    e.preventDefault();
    const name = document.getElementById("reviewName1").value;
    const comment = document.getElementById("reviewComment1").value;
  
    // Create a new review element
    const newReview = document.createElement("div");
    newReview.classList.add("mb-3");
    newReview.innerHTML = `<strong>${name}:</strong><p>${comment}</p>`;
  
    // Append the new review
    document.getElementById("reviews1").appendChild(newReview);
  
    // Clear the form
    this.reset();
  });

  // checkout function

  function buyNow(productName, price) {
    alert(`You selected "${productName}" for $${price.toFixed(2)}. Proceeding to checkout!`);
    // Redirect to checkout page
    window.location.href = `checkout.php?product=${encodeURIComponent(productName)}&price=${price}`;
  }

  //cart 
  
  
  
  
