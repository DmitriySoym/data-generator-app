document.addEventListener("DOMContentLoaded", () => {
  const inputRangeValue = document.querySelector(".input__range");
  const seedValue = document.querySelector(".input__seed");
  const randomBtn = document.querySelector(".btn-danger");
  const table = document.querySelector(".users__table");
  const changeRegion = document.querySelector(".region_select");

  inputRangeValue.addEventListener("change", (event) => {
    seedValue.value = event.target.value * 100;
  });

  seedValue.addEventListener("change", (event) => {
    inputRangeValue.value = event.target.value / 100;
  });

  seedValue.addEventListener("input", (event) => {
    if (event.target.value > 1000) {
      seedValue.value = 1000;
    }
    if (event.target.value < 0) {
      seedValue.value = 0;
    }
  });

  randomBtn.addEventListener("click", () => {
    seedValue.value = Math.floor(Math.random() * 1000);
    inputRangeValue.value = seedValue.value / 100;
  });

  let userData;

  const options = {
    seed: 0,
    page: 1,
    limit: 20,
    language: "en_US",
  };
  function fetchData() {
    console.log("Fetching data...");
    return fetch("/data/generator", {
      method: "POST",
      body: JSON.stringify({
        ...options,
      }),
      headers: {
        "Content-type": "application/json; charset=UTF-8",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        userData = data;
        options.page++;
      })
      .then(renderTable);
  }

  fetchData();

  table.addEventListener("scroll", (event) => {
    if (table.scrollHeight - Math.floor(table.scrollTop) === table.clientHeight) {
      fetchData();
    } else if (options.page >= 6 && table.scrollHeight - Math.floor(table.scrollTop) === table.clientHeight + 1) {
      fetchData();
    }
  });

  changeRegion.addEventListener("change", (event) => {
    table.innerHTML = "";
    options.language = event.target.value;
    fetchData();
  });

  async function renderTable() {
    if (userData === undefined) return;
    else {
      for (let i = 0; i < userData.length; i++) {
        const tableItem = document.createElement("li");
        tableItem.classList.add("users__table-item");

        if (i % 2 === 0) tableItem.classList.add("users__table-item--even");

        tableItem.innerHTML = `
            <span class="user__number">${userData[i].number}</span>
            <div class="users__table-item-text">${userData[i].ID}</div>
            <div class="users__table-item-text">${userData[i].name}</div>
            <div class="users__table-item-text">${userData[i].adress}</div>
            <div class="users__table-item-text">${userData[i].phone}</div>
        `;
        table.appendChild(tableItem);
      }
    }
  }
});
