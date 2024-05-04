document.addEventListener("DOMContentLoaded", () => {
  const inputRangeValue = document.querySelector(".input__range-error");
  const inputNumberValue = document.querySelector(".input__number-error");
  const inputSeedValue = document.querySelector(".input__seed");
  const randomBtn = document.querySelector(".btn-danger");
  const table = document.querySelector(".users__table");
  const changeRegion = document.querySelector(".region_select");
  let userData;

  inputRangeValue.addEventListener("change", (event) => {
    inputNumberValue.value = event.target.value * 100;
  });

  inputNumberValue.addEventListener("change", (event) => {
    inputRangeValue.value = event.target.value / 100;
  });

  inputNumberValue.addEventListener("input", (event) => {
    if (event.target.value > 1000) {
      inputNumberValue.value = 1000;
    }
    if (event.target.value < 0) {
      inputNumberValue.value = 0;
    }
  });

  randomBtn.addEventListener("click", () => {
    inputSeedValue.value = Math.floor(Math.random() * 100000);
    options.seed = inputSeedValue.value;
    useSeed();
  });

  inputSeedValue.addEventListener("input", () => {
    options.seed = inputSeedValue.value;
    useSeed();
  });

  function useSeed() {
    table.innerHTML = "";
    userData = [];
    options.page = 1;
    fetchData();
  }

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

  table.addEventListener("scroll", () => {
    if (table.scrollHeight - Math.floor(table.scrollTop) === table.clientHeight) {
      fetchData();
    } else if (options.page >= 6 && table.scrollHeight - Math.floor(table.scrollTop) === table.clientHeight + 1) {
      fetchData();
    }
  });

  changeRegion.addEventListener("change", (event) => {
    table.innerHTML = "";
    options.page = 1;
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
            <span class="user__number">${i + 1 + (options.page - 2) * options.limit}</span>
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
