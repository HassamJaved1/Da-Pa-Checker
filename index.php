<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <title>DA PA Checker</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .outer {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 70px;
        }
       #loading {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3), 0 0 5px rgba(0, 0, 0, 0.2), 0 0 8px rgba(0, 0, 0, 0.1);
    background-color: #f8f8f8;
    border-radius: 15px;
}
#resultTable{
    width: 770px;
}
#warning-message{
      display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 5px;
}
    </style>
</head>
<body>
    <!-- <nav class='navbar is-danger'>
    </nav>     -->
    <div class='outer'>
        <div class='field'>
            <div class='control'>
                <textarea padding:15px; rows='5' id='txt-area' style='width:33rem; height: 13rem;' class='textarea is-danger textarea is-responsive textarea is-hovered textarea has-fixed-size' placeholder= "Check Website's URL"></textarea>
                <div class="word-count" id="word-count">Websites: 0/10</div>
            </div>
        </div>
        <div>
            <button id='fetchCookiesBtn' class='button is-medium is-responsive button is-danger is-rounded'>Check Websites</button>
        </div> 
    </div>
    <div style="display:flex; align-items:center; justify-content:center;"><article id="warning-message" style="width:35rem;" class="message is-danger is-small">
  <div class="message-header">
    <p>Message</p>
    <button onclick="document.getElementById('warning-message').style.display = 'none';"
 class="delete" aria-label="delete"></button>
  </div>
  <div class="message-body" id="message-body">
  </div>
</article>
</div>
      <div style="display:flex; align-items:center; justify-content:center;flex-direction:column;">  <div class='table-container'>
            <table id="resultTable" class='table is-striped table is-narrow'>
                <thead style="background-color:#f8f8f8;" >
                    <th><abbr title='WebPage'>WebPage</abbr></th>
                    <th><abbr title='DA'>Domain Authority</abbr></th>
                    <th><abbr title='PA'>Page Authority</abbr></th>
                    <th><abbr title='SS'>Spam Score</abbr></th>
                    <th><abbr title='MR'>Moz Rank</abbr></th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>   
    <div id="loading">
        <img src="https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif" alt="Loading..." width="200" height="200" />
    </div>
<script>
        const textArea = document.getElementById('txt-area');
        const wordCount = document.getElementById('word-count');
        let lastValidContent = "";
        textArea.addEventListener('input', function() {
            const words = textArea.value.trim().split(/\s+/).filter(Boolean);
            const wordLimit = 10;
            if (words.length > wordLimit) {
                textArea.value = lastValidContent;
            } else {
                lastValidContent = textArea.value;
                wordCount.textContent = `Websites: ${words.length}/${wordLimit}`;
            }
        });
$(document).ready(function () {
    $("#fetchCookiesBtn").click(function () {
    //Remove extra code
        let Website = $("#txt-area").val().trim().split(/\s+/);
         if (Website.length === 0 || Website[0] === "") {
            var msgblock= document.getElementById("warning-message");
            var msgid=document.getElementById("message-body");
            msgblock.style.display = "block";
            msgid.textContent="Textarea is empty. Please enter some content.";
        }
         else {
            document.getElementById('loading').style.display = "block";
            $.ajax({
                type: "POST",
                url: "handle_ajax.php",
                data: { website: Website },
                dataType: 'json',
                success: function (response) {
                    document.getElementById('loading').style.display = "none";
                    let tableBody = $("#resultTable tbody");
                        tableBody.empty();
                        console.log(response);
              response.forEach((response)=>{
              let data = response.data;
                    console.log(data);    
                    if (data == "Validity Check!") {
                        let row = $("<tr>");
                        row.append($("<td colspan='5'>").text("Oops! Your URL is incorrect"));
                        tableBody.append(row);
                    }
                    else if(data=="In Progress!"){
                        let row = $("<tr>");
                        row.append($("<td colspan='5'>").text("Request in Progress..."));
                        tableBody.append(row);
                    }
                    
                    else {
                        data.forEach(function (item) {
                            let row = $("<tr>");
                            row.append($("<td>").text(item.domain));
                            row.append($("<td>").text(item.site_da));
                            row.append($("<td>").text(item.site_pa));
                            row.append($("<td>").text(item.spam_score=="premium" ? item.spam_score : item.spam_score + "%" ));
                            row.append($("<td>").text(item.site_mr));
                            tableBody.append(row);
                        });
                    }});
                },
                error: function (xhr, status, error) {
                    document.getElementById('loading').style.display = "none";
                    console.error(xhr.responseText);
                } });
        }
    });
});
</script>
</body>
</html>