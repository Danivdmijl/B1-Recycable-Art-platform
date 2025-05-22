## Uitleg code password-form



## Deze code is gemaakt voor een statiche pagina

## wat doet deze code precies ?

## Twee invoervelden voor een wachtwoord en een bevestiging ervan.

## Een oogicoontje om het wachtwoord zichtbaar/onzichtbaar te maken.

## Een melding die laat zien of de wachtwoorden overeenkomen.

## Een submit-knop met validatie.



## HTML structuur van de pagina

## De form bevat twee invoervelden: #password en #confirm-password.

## Beiden invloedvelden hebben een oogicoon om het wachtwoord zichtbaar te maken via JavaScript.

## Onder de invoervelden staat een div waarin een melding komt als de wachtwoorden overeenkomen. 

## De submit knop roept de validateForm() functie aan om het formulier te controleren



## CSS stijl van de pagina

## De body heeft een grijze achtergrond

## De .custom-password-wrapper class zorgt voor het witte formulierblok in het midden met padding,border-radius aan de zijkanten en een schaduw

## de input-velden zijn mooi gestyled met border-radius, zachte kleiren een een focus-effect

## .password-icon plaatst het oogicoontje rechts in het invoerveld.

## .submit-button heeft een groene stijl met een hover-effect.



## JavaScript functionaliteit van de pagina

## togglePassword() maakt het wachtwoord zichtbaar of verborgen bij het klikken op het oogicoon:

## input.setAttribute('type', 'text');  // toont het wachtwoord
## input.setAttribute('type', 'password');  // verbergt het wachtwoord

## het icoon verandert ook van fa-eye naar fa-eye-slash.

## checkPasswords() controleert de inputvelden

## als bijde wachtwoorden overeen komen dan zie je “Wachtwoorden komen overeen”.



## validateForm() wordt uitgevoerd bij het verzenden van het formulier

## Controleert of beide velden zijn ingevuld.

## Controleert of de wachtwoorden overeenkomen.

## Toont foutmeldingen via alert() als iets niet klopt.

## Geeft een succesmelding als alles goed is.



## dit formulier heeft geen backend en wordt nergens naar verzonden het is bedoeld voor een statische pagina.

