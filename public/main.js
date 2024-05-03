document.addEventListener("DOMContentLoaded", () => {
  const inputRangeValue = document.querySelector(".input__range");
  const seedValue = document.querySelector(".input__seed");
  const randomBtn = document.querySelector(".btn-danger");

  inputRangeValue.addEventListener("change", (event) => {
    seedValue.value = event.target.value * 100;
  });

  seedValue.addEventListener("change", (event) => {
    inputRangeValue.value = event.target.value / 100;
    console.log(inputRangeValue.value);
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
});
