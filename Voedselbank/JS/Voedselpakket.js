// Show all clients when clicking on search box
function showClientList() {
  document.getElementById("clientList").style.display = "block";
}

// Filter clients based on search input
function filterClients() {
  const input = document.getElementById("clientSearch");
  const filter = input.value.toLowerCase();
  const clientList = document.getElementById("clientList");
  const clientOptions = clientList.getElementsByTagName("div");

  for (let i = 0; i < clientOptions.length; i++) {
    const txtValue = clientOptions[i].textContent || clientOptions[i].innerText;
    if (txtValue.toLowerCase().indexOf(filter) > -1) {
      clientOptions[i].style.display = "";
    } else {
      clientOptions[i].style.display = "none";
    }
  }
}

// Select client when clicked, hide dropdown and show dieetwensen
function selectClient(id, name) {
  document.getElementById("selectedClientId").value = id;
  document.getElementById("clientSearch").value = name;
  document.getElementById("clientList").style.display = "none";
  showDieetwensen(id); // Show dietary preferences
}

// Show dietary preferences for the selected client
function showDieetwensen(klantId) {
  const display = document.getElementById("dieetwensen_display");
  if (dieetwensen[klantId]) {
    display.innerHTML =
      "<strong>Dieetwensen:</strong> " + dieetwensen[klantId].join(", ");
  } else {
    display.innerHTML = "";
  }
}

// Show all products when clicking on search box
function showProductList() {
  document.getElementById("productList").style.display = "block";
}

// Filter products based on search input
function filterProducts() {
  const input = document.getElementById("productSearch");
  const filter = input.value.toLowerCase();
  const productList = document.getElementById("productList");
  const productOptions = productList.getElementsByTagName("div");

  for (let i = 0; i < productOptions.length; i++) {
    const txtValue =
      productOptions[i].textContent || productOptions[i].innerText;
    if (txtValue.toLowerCase().indexOf(filter) > -1) {
      productOptions[i].style.display = "";
    } else {
      productOptions[i].style.display = "none";
    }
  }
}

// Select product and show quantity field
function selectProduct(id, name, max, category) {
  document.getElementById("selectedProductId").value = id;
  document.getElementById("productSearch").value = name;
  document.getElementById("productQuantity").max = max;
  document.getElementById("selectedCategoryId").value = category;
  document.getElementById("productList").style.display = "none";
}

// Add product to the list
function addProduct() {
  const productId = document.getElementById("selectedProductId").value;
  const productName = document.getElementById("productSearch").value;
  const quantity = document.getElementById("productQuantity").value;
  const categoryId = document.getElementById("selectedCategoryId").value;

  if (!productId || !quantity) {
    alert("Selecteer een product en vul een aantal in.");
    return;
  }

  const selectedProductsContainer = document.getElementById(
    "selectedProductsContainer"
  );
  const productHtml = `
      <div class="selected-product">
          <span>${productName} (Aantal: ${quantity})</span>
          <input type="hidden" name="producten[]" value="${productId}">
          <input type="hidden" name="quantities[]" value="${quantity}">
          <input type="hidden" name="categorie_ids[]" value="${categoryId}">
      </div>
  `;
  selectedProductsContainer.innerHTML += productHtml;

  // Clear input fields after adding product
  document.getElementById("productSearch").value = "";
  document.getElementById("productQuantity").value = "";
}

// Hide dropdown if clicking outside
window.onclick = function (event) {
  if (!event.target.matches("#clientSearch")) {
    document.getElementById("clientList").style.display = "none";
  }
  if (!event.target.matches("#productSearch")) {
    document.getElementById("productList").style.display = "none";
  }
};
