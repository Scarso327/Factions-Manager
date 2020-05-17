<div id="hModal" class="modal">
    <div class="modal-body">
        <div class="modal-header">
            <h3>Log View</h3>
            <i class="fas fa-times-circle" id="hModal-close"></i>
        </div>
        <div class="modal-content roster">
            <table>
                <tr class = "first">
                    <th COLSPAN = 4>Details</th>
                </tr>
                <tr class = "second">   
                    <!--- Details --->
                    <th>Date</th>
                    <th><?=View::getLanguage($this->params["history"]['type'], "-member-log-title");?> <?=View::getLanguage(Faction::$var, "-member-title");?></th>
                    <th>Action</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td id="modal-history-date"></td>
                    <td id="modal-history-member"></td>
                    <td id="modal-history-action"></td>
                    <td id="modal-history-status"></td>
                </tr>
            </table>
            <div id="log-info">

            </div>
        </div>
    </div>
</div>
<script>
    var hModal = document.getElementById("hModal");
    var info = document.getElementById("log-info");

    document.getElementById("hModal-close").onclick = function() { closeHModal(); };

    hModal.onclick = function(event) { 
        if (event.target == hModal) { 
            closeHModal ();
        }
    }

    function showHModal(btn) {
        var logID = btn.getAttribute("data-id");
        var logRow = document.getElementById("log-"+logID);

        document.getElementById("modal-history-date").innerHTML = logRow.childNodes[1].innerHTML;
        document.getElementById("modal-history-member").innerHTML = logRow.childNodes[3].innerHTML;

        var action = document.getElementById("modal-history-action");
        var status = document.getElementById("modal-history-status");

        action.innerHTML = logRow.childNodes[5].innerHTML;
        status.innerHTML = logRow.childNodes[7].innerHTML;
        action.classList = logRow.childNodes[5].classList;
        status.classList = logRow.childNodes[7].classList;

        $.ajax({
            type: "POST",
            url: "<?=URL;?>api/responses/<?=Faction::$var;?>/",
            data: {id: logID},
            dataType: 'JSON',
            success: function(response){
                if (response.result != "success") {
                    console.log("Request Failed with Reason: " + response.reason);
                    alert("An error occured...");
                } else {
                    (response.responses).forEach(createField);
                }
            }
        });

        hModal.style.display = "block";
    }

    function closeHModal () {
        hModal.style.display = "none";

        while (info.hasChildNodes()) {  
            info.removeChild(info.firstChild);
        }
    }

    function createField (field) {
        var title = document.createElement("H4");
        title.innerHTML = field.name;
        info.appendChild(title);

        var value = document.createElement("P");
        value.innerHTML = field.value;
        info.appendChild(value);
    }
</script>