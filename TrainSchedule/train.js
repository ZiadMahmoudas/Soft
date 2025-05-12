document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.getElementById("train-schedule");
  
    tbody.innerHTML = `<tr><td colspan="4">Loading train schedules...</td></tr>`;

        const response = await fetch("exampleUsage.php");
        const trainSchedule = await response.json();

        if (trainSchedule.length > 0) {
            tbody.innerHTML = ""; 
            trainSchedule.forEach((train) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${train.Train_name}</td>
                    <td>${train.Station_name}</td>
                    <td>${train.Departure_time}</td>
                    <td>${train.Arrival_time}</td>`;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4">No train schedules available.</td></tr>`;
        }
    

    await fetchNotificationState();
});

async function fetchNotificationState() {
        const response = await fetch("getNotificationState.php");
        const data = await response.json();
            showNotification(data.message);
   
}

const logout = document.querySelector("#logout");
logout.addEventListener("click", () => {
    window.location.href = "http://localhost/Soft/auth/signup.html";
    localStorage.removeItem("lang");
    localStorage.removeItem("theme");
});

// Function to show notification bar
function showNotification(message) {
    const notificationBar = document.createElement("div");
    notificationBar.id = "notificationBar";
    notificationBar.style.position = "fixed";
    notificationBar.style.top = "0";
    notificationBar.style.left = "0";
    notificationBar.style.width = "100%";
    notificationBar.style.backgroundColor = "#ffcc00";
    notificationBar.style.color = "#000";
    notificationBar.style.textAlign = "center";
    notificationBar.style.padding = "10px";
    notificationBar.style.zIndex = "1000";
    notificationBar.style.display = "flex";
    notificationBar.style.justifyContent = "space-between";
    notificationBar.style.alignItems = "center";

    const messageSpan = document.createElement("span");
    messageSpan.textContent = message;

    const closeButton = document.createElement("button");
    closeButton.textContent = "X";
    closeButton.style.background = "none";
    closeButton.style.border = "none";
    closeButton.style.color = "#000";
    closeButton.style.fontSize = "16px";
    closeButton.style.cursor = "pointer";
    closeButton.style.marginLeft = "10px";

    closeButton.addEventListener("click", () => {
        notificationBar.remove();
    });

    notificationBar.appendChild(messageSpan);
    notificationBar.appendChild(closeButton);

    document.body.appendChild(notificationBar);

    setTimeout(() => {
        notificationBar.remove();
    }, 150000); 
}
