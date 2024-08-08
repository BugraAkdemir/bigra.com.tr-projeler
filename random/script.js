const randomNumber = Math.floor(Math.random() * 21);
document.getElementById("makeNumberText").innerHTML = "?";
let tik = 5;
let say = 0;
document.getElementById("guessNumberButton").addEventListener("click", function () {
    const userGuess = parseInt(document.getElementById("numberInput").value);
    const resultText = document.getElementById("makeNumberText");

    say++;

    if (userGuess === randomNumber) {
        resultText.textContent = "Doğru! Tebrikler!";
       
    } else{
        if(tik == say){
            tik = 0;
            say = 0;
            resultText.innerHTML = '5 Hakkınızı Doldurdunuz!!'
        }
    }
});


function refreshPage() {
    location.reload();
}




