const kaartgegevens = [ // alle gegevens van de verschillende kaartjes
    {
      titel: "Contacts info",
      velden: [
        { vraag: "First Name*", type: "text", name: "remaker-firstname" },
        { vraag: "Last Name*", type: "text", name: "remaker-lastname" },
        { vraag: "Email*", type: "email", name: "remaker-email" },
        { vraag: "Phone*", type: "tel", name: "remaker-phone" }
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
          { vraag: "Upload here photos for the first phase: Raw (material)*", type: "image", name: "remaker-materialProcess-1" }
        ]
    },
    {
        titel: "Upload at least 1 photo for each phase of the 3 phases of your art making process",
        velden: [
          { vraag: "Upload here photos for the second phase: Remaking (Making process)*", type: "image", name: "remaker-materialProcess-2" }
        ]
    },
    {
        titel: "Upload at least 1 photo for each phase of the 3 phases of your art making process",
        velden: [
          { vraag: "Upload here photos for the third phase: Revealing (your masterpiece)*", type: "image", name: "remaker-materialProcess-3" }
        ]
    },
    {
      titel: "Thank you!",
      velden: []
    }
  ];

  let current = 0; // houdt bij bij welk kaartje je bent
  const antwoorden = {}; // om de antwoorden op te slaan
  const formContainer = document.getElementById("form"); // de container waar alles ingeplaats moet worden

  function renderCard(index) {
    formContainer.innerHTML = ""; // leegt de huidige kaart

    const kaart = kaartgegevens[index]; // pak de gegevens van de kaartjes

    const article = document.createElement("article");
    article.className = `RemakerForm-wrapper RemakerForm-wrapper-${index + 1}`;

    const title = document.createElement("h2");
    title.className = "RemakerForm-title";
    title.textContent = kaart.titel;
    article.appendChild(title);

    const form = document.createElement("form"); // hier worden de form settings ingesteld
    form.className = "RemakerForm";
    form.setAttribute("method", "POST");
    form.setAttribute("action", "processRemaker.php");

    kaart.velden.forEach(v => { // hier worden alle input velden met labels aangemaakt
      const label = document.createElement("label");
      label.textContent = v.vraag;

      const input = document.createElement("input");
      input.type = v.type;
      input.name = v.name;
      input.required = true;
      if (antwoorden[v.name]) input.value = antwoorden[v.name];

      input.addEventListener("input", e => {
        antwoorden[v.name] = e.target.value;
      });

      form.appendChild(label);
      form.appendChild(input);
    });

    article.appendChild(form);

    // Buttons
    const buttonContainer = document.createElement("div");
    buttonContainer.className = "RemakerForm-buttons";

    const prevBtn = document.createElement("button"); 
    prevBtn.className = "RemakerForm-previous RemakerForm-button";
    prevBtn.type = "button";
    prevBtn.textContent = "Previous";

    if (index === 0){ // schakelt de previous button uit op het eerste kaartje
        prevBtn.disabled = index === 0;
        prevBtn.style.display = "none";
        buttonContainer.style.justifyContent = "flex-end"
    }
    prevBtn.onclick = () => { // als je erop klikt renderd hij het vorige kaartje
      current--;
      renderCard(current);
    };

    const nextBtn = document.createElement("button");
    nextBtn.className = "RemakerForm-next RemakerForm-button";
    if (index < kaartgegevens.length - 1) { // laadt de volgende pagina in zolang je niet op de laatste zit
      nextBtn.type = "button";
      nextBtn.textContent = "Next";
      nextBtn.onclick = () => {
        current++;
        renderCard(current);
      };
    } else { // als je op de laatste pagina zit veranderd de button naar een verzend knop
      nextBtn.type = "submit";
      nextBtn.textContent = "Submit";
    }

    buttonContainer.appendChild(prevBtn); // voegt de buttons doe aan de container 
    buttonContainer.appendChild(nextBtn);
    article.appendChild(buttonContainer); // voegt de container met buttons toe aan het kaartje

    // Progress bar
    const progressBarContainer = document.createElement("div");
    progressBarContainer.className = "RemakerForm-progressBar";

    const progress = document.createElement("div");
    progress.className = "RemakerForm-progress";
    progress.style.width = ((index + 1) / kaartgegevens.length) * 100 + "%"; // kijkt gebaseerd op het aantal kaartjes hoeveel width hij krijgt

    if(progress.style.width == 100 + "%"){
        progress.style.borderBottomRightRadius = 1.3 + "rem";
    }

    progressBarContainer.appendChild(progress);
    article.appendChild(progressBarContainer);

    formContainer.appendChild(article);
  }

  renderCard(current);