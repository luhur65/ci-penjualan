$(document).ready(function () {
    startTime()
});

function startTime() {
	setInterval(() => {
		let date = new Date();

		let day = date.toLocaleString("id", {
			dateStyle: "medium",
		});

		let time = date.toLocaleString("id", {
			timeStyle: "medium",
		});

		$("nav .datetime-place .date-place").html(day);
		$("nav .datetime-place .time-place").html(time.replaceAll(".", ":"));
	}, 1000);
}
