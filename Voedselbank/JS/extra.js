const modal = document.getElementById("editModal");
const closeBtn = document.getElementsByClassName("close")[0];
const editButtons = document.getElementsByClassName("edit-btn");

for (let button of editButtons) {
  button.onclick = function () {
    const id = this.getAttribute("data-id");
    const name = this.getAttribute("data-name");
    document.getElementById("editNaam").value = name;
    document.getElementById("editId").value = id;
    modal.style.display = "block";
  };
}

closeBtn.onclick = function () {
  modal.style.display = "none";
};

window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
