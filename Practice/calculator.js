

const display = document.getElementById("display");
function appendToDisplay(input){
    display.value += input;



}
function clearDisplay(){
    display.value ="";


}

function calculate(){
    try{
        dislay.value = eval(display.value);

    }
    catch(error){
        display.value = "error"

    }    

}