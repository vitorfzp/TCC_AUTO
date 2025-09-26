document.addEventListener("DOMContentLoaded", function () {
    // Menu Responsivo
    const closeBtn = document.getElementById("fechar");
    const menuBtn = document.getElementById("men");
    const menuLinks = document.querySelector(".menu-links");
  
       // Fechar Menu
       closeBtn.addEventListener("click", () => {
        menuLinks.style.display = "none";
      });
    // Abrir Menu
    menuBtn.addEventListener("click", () => {
      menuLinks.style.display = "flex";
    });
  
  
    // Botão de Enviar Feedback
    const feedbackBtn = document.querySelector(".content button");
    feedbackBtn.addEventListener("click", () => {});

        // Efeito no botão ao tirar o mouse
        feedbackBtn.addEventListener("mouseout", () => {
          feedbackBtn.style.backgroundColor = "#fff";
          feedbackBtn.style.color = "#000";
          });

    // Efeito no botão ao passar o mouse
    feedbackBtn.addEventListener("mouseover", () => {
      feedbackBtn.style.backgroundColor = "#ffcc00";
      feedbackBtn.style.color = "#000";
    });
  
  });

  