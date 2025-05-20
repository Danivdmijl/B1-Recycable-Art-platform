const kaartgegevens = [
  {
    titel: "Contacts info",
    velden: [
      { vraag: "First Name*", type: "text", name: "remaker-firstname" },
      { vraag: "Last Name*", type: "text", name: "remaker-lastname" },
      { vraag: "Email*", type: "email", name: "remaker-email" },
      { vraag: "Phone", type: "tel", name: "remaker-phone" }
    ]
  },
  {
    titel: "Address",
    velden: [
      { vraag: "Country*", type: "text", name: "remaker-country" },
      { vraag: "City*", type: "text", name: "remaker-city" }
    ]
  },
  {
    titel: "Brand name (if applicable)",
    velden: [
      { vraag: "Brandname", type: "text", name: "remaker-brandname" }
    ]
  },
  {
    titel: "Social media links (Instagram, Facebook, YouTube, etc...)",
    velden: [
      { vraag: "Insert your social media pages links", type: "text", name: "remaker-socials"}
    ]
  },
  {
    titel: "How did you hear about us",
    velden: [
      { vraag: "Type your answer here", type: "text", name: "remaker-refferal"}
    ]
  },
  {
    titel: "Tell us about your sustainable materials and processes",
    velden: [
      { vraag: "your materials and process*", type: "text", name: "remaker-materialProcess" }
    ]
  },
  {
    titel: "Upload at least 1 photo for each phase of the 3 phases of your art making process",
    velden: [
      { vraag: "Upload here photos for the first phase: Raw (material)*", type: "file", name: "remaker-materialProcess-1" }
    ]
  },
  {
    titel: "Upload at least 1 photo for each phase of the 3 phases of your art making process",
    velden: [
      { vraag: "Upload here photos for the second phase: Remaking (Making process)*", type: "file", name: "remaker-materialProcess-2" }
    ]
  },
  {
    titel: "Upload at least 1 photo for each phase of the 3 phases of your art making process",
    velden: [
      { vraag: "Upload here photos for the third phase: Revealing (your masterpiece)*", type: "file", name: "remaker-materialProcess-3" }
    ]
  },
  {
    titel: "Thank you!",
    velden: []
  }
];

const backgroundImage = document.createElement("img");
backgroundImage.draggable = false;
backgroundImage.classList.add("background-layer");
backgroundImage.src = "https://arat-wp.duckduckdev.nl/wp-content/uploads/2025/05/Landing-background.webp";
document.body.appendChild(backgroundImage);

let current = 0;
const antwoorden = {};
const formContainer = document.getElementById("form");

const form = document.createElement("form");
form.className = "RemakerForm";
form.setAttribute("method", "POST");
form.setAttribute("action", "results.php");
form.setAttribute("enctype", "multipart/form-data");
formContainer.appendChild(form);

function renderCard(index) {
  form.innerHTML = ""; // Clear form

  const kaart = kaartgegevens[index];

  const article = document.createElement("article");
  article.className = `RemakerForm-wrapper RemakerForm-wrapper-${index + 1}`;

  // Reset and retrigger animation
  form.classList.remove("popup"); // just in case
  void form.offsetWidth; // forces reflow
  form.classList.add("popup");

  const title = document.createElement("h2");
  title.className = "RemakerForm-title";
  title.textContent = kaart.titel;
  article.appendChild(title);

  kaart.velden.forEach(v => {
    const inputId = `input-${v.name}`;
    const label = document.createElement("label");
    label.setAttribute("for", inputId);
    label.textContent = v.vraag;

    const input = document.createElement("input");
    input.type = v.type;
    input.name = v.name;
    input.id = inputId;
    input.required = true;

    if (v.type !== "file" && antwoorden[v.name]) {
      input.value = antwoorden[v.name];
    }

    input.addEventListener("input", e => {
      if (v.type !== "file") {
        antwoorden[v.name] = e.target.value;
      }
    });

    article.appendChild(label);
    article.appendChild(input);
  });

  // Add hidden fields for previous answers
  for (let i = 0; i < kaartgegevens.length; i++) {
    if (i === index) continue;
    kaartgegevens[i].velden.forEach(v => {
      if (v.type !== "file" && antwoorden[v.name]) {
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = v.name;
        hiddenInput.value = antwoorden[v.name];
        form.appendChild(hiddenInput);
      }
    });
  }

  form.appendChild(article);

  // Buttons
  const buttonContainer = document.createElement("div");
  buttonContainer.className = "RemakerForm-buttons";

  const prevBtn = document.createElement("button");
  prevBtn.className = "RemakerForm-previous RemakerForm-button";
  prevBtn.type = "button";
  prevBtn.textContent = "Previous";

  if (index === 0) {
    prevBtn.disabled = true;
    prevBtn.style.display = "none";
    buttonContainer.style.justifyContent = "flex-end";
  }

  prevBtn.onclick = () => {
    current--;
    renderCard(current);
  };

  const nextBtn = document.createElement("button");
  nextBtn.className = "RemakerForm-next RemakerForm-button";

  if (index < kaartgegevens.length - 1) {
    nextBtn.type = "button";
    nextBtn.textContent = "Next";
    nextBtn.onclick = () => {
      current++;
      renderCard(current);
    };

    buttonContainer.appendChild(prevBtn);
    buttonContainer.appendChild(nextBtn);
    form.appendChild(buttonContainer);
  } else {
    nextBtn.type = "submit";
    nextBtn.textContent = "Submit";

    const buttonFormContainer = document.createElement("div");
    buttonFormContainer.className = "buttonFormContainer";
    buttonFormContainer.appendChild(prevBtn);
    buttonFormContainer.appendChild(nextBtn);
    form.appendChild(buttonFormContainer);
  }

  // Progress Bar
  const progressBarContainer = document.createElement("div");
  progressBarContainer.className = "RemakerForm-progressBar";

  const progress = document.createElement("div");
  progress.className = "RemakerForm-progress";
  progress.style.width = ((index + 1) / kaartgegevens.length) * 100 + "%";

  if (progress.style.width === "100%") {
    progress.style.borderBottomRightRadius = "1.3rem";
  }

  progressBarContainer.appendChild(progress);
  form.appendChild(progressBarContainer);


  // throw out an error message whenever error is caught
function showErrorMessage() {
  alert("Please fill in all the necessary fields with a *");
}

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get("error") === "missing_fields") {
    showErrorMessage();
    const url = new URL(window.location);
    url.searchParams.delete("error");
    window.history.replaceState({}, document.title, url.pathname + url.search);
  }
}

renderCard(current);