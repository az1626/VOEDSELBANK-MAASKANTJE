document.getElementById("openAddModalBtn").onclick = function () {
  document.getElementById("addModal").style.display = "block";
};

document.getElementById("closeAddModal").onclick = function () {
  document.getElementById("addModal").style.display = "none";
};

document.getElementById("closeEditModal").onclick = function () {
  document.getElementById("editModal").style.display = "none";
};

const editButtons = document.querySelectorAll(".editBtn");
editButtons.forEach((button) => {
  button.onclick = function () {
    document.getElementById("editId").value = this.getAttribute("data-id");
    document.getElementById("editNaam").value = this.getAttribute("data-naam");
    document.getElementById("editAdres").value =
      this.getAttribute("data-adres");
    document.getElementById("editContactpersoon").value = this.getAttribute(
      "data-contactpersoon"
    );
    document.getElementById("editTelefoonnummer").value = this.getAttribute(
      "data-telefoonnummer"
    );
    document.getElementById("editEmail").value =
      this.getAttribute("data-email");
    document.getElementById("editEerstevolgende_levering").value =
      this.getAttribute("data-eerstevolgende_levering");

    document.getElementById("editModal").style.display = "block";
  };
});
