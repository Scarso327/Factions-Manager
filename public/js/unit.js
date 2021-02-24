class Unit {

    constructor() {
        $("select[rank-dropdown]").each(function () {
            $(this).change(function () {
                var dropdown = $(this);
                Unit.changeRank(dropdown.data("faction"), dropdown.data("steamid"), dropdown.data("unitid"), this.value);
            });
        });
    }

    static changeRank(faction, steamid, unitid, rankid) {
        $.ajax({
            type: "POST",
            url: "http://localhost/api/unit/",
            data: {
                faction: faction,
                steamid: steamid,
                unit_id: unitid,
                rank_id: rankid
            },
            success: function(response) {}
        });
    }
}

$(document).ready(function () {
    unit = new Unit();
});