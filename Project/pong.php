<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
//modified from http://jsfiddle.net/bencentra/q1s8gmqv/?utm_source=website&utm_medium=embed&utm_campaign=q1s8gmqv
var canvas;
var context;
var loop;
var leftPaddle;
var rightPaddle;
var ball;
var paddleWidth = 20;
var paddleHeight = 100;
var ballSize = 14;
var ballSpeed = 4;
var paddleSpeed = 4;
var drawables = [];
// Key Codes
var W = 87;
var S = 83;
var UP = 38;
var DOWN = 40;
let didSend = false;

// Keep track of pressed keys
var keys = {
    W: false,
    S: false,
    UP: false,
    DOWN: false
};

// Keep track of the score
var leftScore = 0;
var rightScore = 0;
function init() {
    didSend = false;
    canvas = document.getElementById("board");
    if (canvas.getContext) {
        context = canvas.getContext("2d");
        leftPaddle = makeRect(25, canvas.height / 2 - paddleHeight / 2, paddleWidth, paddleHeight, 5, '#BC0000');
        rightPaddle = makeRect(canvas.width - paddleWidth - 25, canvas.height / 2 - paddleHeight / 2, paddleWidth, paddleHeight, 5, '#0000BC');
        ball = makeRect(0, 0, ballSize, ballSize, ballSpeed, '#008000');
        drawables.push(leftPaddle);
        drawables.push(rightPaddle);
        drawables.push(ball);
        console.log(drawables);
        resetBall();
        attachKeyListeners();
        loop = window.setInterval(gameLoop, 16); //16ms
        canvas.focus();
    }
}
function resetBall() {
    ball.x = canvas.width / 2 - ball.w / 2;
    ball.y = canvas.height / 2 - ball.w / 2;
    // Modify the ball object to have two speed properties, one for X and one for Y
    ball.sX = ballSpeed;
    ball.sY = ballSpeed / 2;

    // Randomize initial direction
    if (Math.random() > 0.5) {
        ball.sX *= -1;
    }
    // Randomize initial direction
    if (Math.random() > 0.5) {
        ball.sY *= -1;
    }
}
// Bounce the ball off of a paddle
function bounceBall() {
    // Increase and reverse the X speed
    if (ball.sX > 1) {
        ball.sX += 1;
        // Add some "curve"
        if (keys.UP) {
            ball.sY -= 0;
        } else if (keys.DOWN) {
            ball.sY += 1;
        }
    } else {
        ball.sX -= 1;
        // Add some "curve"
        if (keys.W) {
            ball.sY -= 1;
        } else if (keys.S) {
            ball.sY += 1
        }
    }
    ball.sX *= -1;
    ball.c = '#4B0082';
}
function attachKeyListeners() {
    // Listen for keydown events
    window.addEventListener('keydown', function (e) {
        console.log("keydown", e);
        if (e.keyCode === W) {
            keys.W = true;
        }
        if (e.keyCode === S) {
            keys.S = true;
        }
        if (e.keyCode === UP) {
            keys.UP = true;
        }
        if (e.keyCode === DOWN) {
            keys.DOWN = true;
        }
        console.log(keys);
    });
    window.addEventListener('keyup', function (e) {
        console.log("keyup", e);
        if (e.keyCode === W) {
            keys.W = false;
        }
        if (e.keyCode === S) {
            keys.S = false;
        }
        if (e.keyCode === UP) {
            keys.UP = false;
        }
        if (e.keyCode === DOWN) {
            keys.DOWN = false;
        }
        console.log(keys);
    });
}
// Create a Rectangle object - for paddles, ball, etc
function makeRect(x, y, width, height, speed, color) {
    if (!color)
        color = '#90EE90';
    return {
        x: x,
        y: y,
        w: width,
        h: height,
        s: speed * 2,
        c: color,
        draw: function () {
            context.fillStyle = this.c;
            context.fillRect(this.x, this.y, this.w, this.h);
        }
    };
}
function doAI() {
    if (ball.x >= canvas.width * .6) {
        let paddleHalf = paddleHeight / 2;
        if (ball.y > rightPaddle.y + paddleHalf) {
            rightPaddle.y += rightPaddle.s;
        } else if (ball.y < rightPaddle.y) {
            rightPaddle.y -= rightPaddle.s;
        }
    }
    clampToCanvas(rightPaddle);
}
function movePaddle() {
    if (keys.W) {
        leftPaddle.y -= leftPaddle.s;
    }
    if (keys.S) {
        leftPaddle.y += leftPaddle.s;
    }
    if (keys.UP) {
        leftPaddle.y -= leftPaddle.s;
    }
    if (keys.DOWN) {
        leftPaddle.y += leftPaddle.s;
    }
    clampToCanvas(leftPaddle);
}
function clampToCanvas(paddle) {
    if (paddle.y < 0) {
        paddle.y = 0;
    }
    if (paddle.y + paddle.h > canvas.height) {
        paddle.y = canvas.height - paddle.h;
    }
}
function moveBall() {
    // Move the ball
    ball.x += ball.sX;
    ball.y += ball.sY;
    // Bounce the ball off the bottom/top
    if (ball.y < 1 || ball.y + ball.h > canvas.height) {
        ball.sY *= -1;
    }
}
function checkPaddleCollision() {
    // Bounce the ball off the paddles
    if (ball.y + ball.h / 2 >= leftPaddle.y && ball.y + ball.h / 2 <= leftPaddle.y + leftPaddle.h) {
        if (ball.x <= leftPaddle.x + leftPaddle.w) {
            bounceBall();
        }
    }
    if (ball.y + ball.h / 2 >= rightPaddle.y && ball.y + ball.h / 2 <= rightPaddle.y + rightPaddle.h) {
        if (ball.x + ball.w >= rightPaddle.x) {
            bounceBall();
        }
    }
}
function checkScore() {
    // Score if the ball goes past a paddle
    if (ball.x < leftPaddle.x) {
        rightScore++;
        resetBall();
        ball.sX *= -1;
    } else if (ball.x + ball.w > rightPaddle.x + rightPaddle.w) {
        leftScore++;
        resetBall();
        ball.sX *= -1;
    }
}
function drawScores() {
    // Draw the scores
    context.fillStyle = '#6495ED';
    context.font = '28px Times New Roman';
    context.textAlign = 'left';
    context.fillText('Score: ' + leftScore, 5, 24);
    context.textAlign = 'right';
    context.fillText('Score: ' + rightScore, canvas.width - 5, 24);
}
function erase() {
    context.fillStyle = '#FFFFFF';
    context.fillRect(1, 1, canvas.width, canvas.height);
}

function gameLoop() {
    erase();
    movePaddle();
    doAI();
    moveBall();

    checkPaddleCollision();
    checkScore();
    drawScores();
    //draw stuff
    for (let i = 0; i < drawables.length; i++) {
        drawables[i].draw();
    }
    if(leftScore >= 8){
	saveScore();
    }
}
function saveScore(){
            if(!didSend){//mutex boolean to block multiple calls from sending repeated data
                didSend = true;
                window.clearInterval(loop);//stops the game loop
                /*let xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        let resp = this.responseText;
                        resp = JSON.parse(resp);
                        //JSON.stringify(resp);
                        console.log("response", resp, resp["data"], resp.data);
                        
                    }
                };
                xhttp.open("POST", "api/saved_scores.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("score="+leftScore);
                */
               $.post("api/saved_scores.php", {score: leftScore}, (data, status)=>{
                    console.log("save callback", data, status);
               });
	}
}
</script>
<?php require(__DIR__."/partials/nav.php");?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You are not logged in, your score won't be saved!");
    //die(header("Location: login.php"));
}
?>
</head>
<body onload="init();">
	<a href="http://bencentra.com/2017-07-11-basic-html5-canvas-games.html">Collection of Canvas based games by Ben Centra</a>
	<main>
		<canvas id="board" width="600px" height="600px" style="border: 1px solid red;">
		
		</canvas>
	</main>
</body>
</html>


<?php require(__DIR__ . "/partials/flash.php");
