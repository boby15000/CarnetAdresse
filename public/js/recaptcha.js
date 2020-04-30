$(function () {
      // Fonction utilisé pour le Captcha
      function recaptchaCallback(token) 
      {
        var elem = document.querySelector(".g-recaptcha");
        while ((elem = elem.parentElement) !== null) 
        {
          if (elem.nodeType === Node.ELEMENT_NODE && elem.tagName === 'FORM') 
          {
            elem.submit();
            break;
          }
        }
      }

}); 