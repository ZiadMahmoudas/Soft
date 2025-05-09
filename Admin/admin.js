const logout = document.querySelector("#logout")
logout.addEventListener("click", function () {
  localStorage.removeItem("lang");
  localStorage.removeItem("theme");
  window.location.href = 'http://localhost/Soft/auth/signup.html';
});
const trainScheduleTable = document.getElementById("trainScheduleTable");
const userTable = document.getElementById("userTable");
const stationTable = document.getElementById("stationTable");

// Fetch Train Schedule
async function fetchTrainSchedule() {
    try {
        const response = await fetch("getTrainSchedule.php");
        const data = await response.json();
        trainScheduleTable.innerHTML = "";
        data.forEach((schedule) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${schedule.Train_name}</td>
                <td>${schedule.Station_name}</td>
                <td>${schedule.Departure_time}</td>
                <td>${schedule.Arrival_time}</td>
                <td>
                    <button class="btn btn-warning btn-sm edit-schedule" data-id="${schedule.id}">Edit</button>
                </td>
            `;
            trainScheduleTable.appendChild(row);
        });

        // Attach event listeners for editing schedules
        document.querySelectorAll(".edit-schedule").forEach((button) => {
            button.addEventListener("click", () => editSchedule(button.dataset.id));
        });
    } catch (error) {
        console.error("Error fetching train schedule:", error);
    }
}

// Fetch Users
async function fetchUsers() {
    try {
        const response = await fetch("getUsers.php");
        const data = await response.json();
        userTable.innerHTML = "";
        data.forEach((user) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${user.User_id}</td>
                <td>${user.User_name}</td>
                <td>
                    <input type="number" class="form-control balance-input" value="${user.Balance}" data-id="${user.User_id}">
                </td>
                <td>
                    <button class="btn btn-primary btn-sm save-balance" data-id="${user.User_id}">Save</button>
                </td>
            `;
            userTable.appendChild(row);
        });

        // Attach event listeners for saving balances
        document.querySelectorAll(".save-balance").forEach((button) => {
            button.addEventListener("click", () => saveBalance(button.dataset.id));
        });
    } catch (error) {
        console.error("Error fetching users:", error);
    }
}

// Save Balance
async function saveBalance(userId) {
    const input = document.querySelector(`.balance-input[data-id="${userId}"]`);
    const newBalance = input.value;

    try {
        const response = await fetch("updateUserBalance.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ User_id: userId, Balance: newBalance }),
        });
        const result = await response.json();
        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Update Successfully',
                text: 'Balance updated successfully.',
                timer: 2000,
                showConfirmButton: true
            });
            fetchUsers();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: result.message
            });
        }
    } catch (error) {
        console.error("Error updating balance:", error);
    }
}

// Fetch Stations
async function fetchStations() {
    try {
        const response = await fetch("getStations.php");
        const data = await response.json();
        stationTable.innerHTML = "";
        data.forEach((station) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${station.station_id}</td>
                <td>${station.station_name}</td>
                <td>${station.city}</td>
            `;
            stationTable.appendChild(row);
        });
    } catch (error) {
        console.error("Error fetching stations:", error);
    }
}

// Add Station
document.getElementById("addStationBtn").addEventListener("click", async () => {
    const stationName = document.getElementById("stationName").value.trim();
    const city = document.getElementById("city").value.trim();

    if (!stationName || !city) {
        alert("Please fill in all fields.");
        return;
    }

    try {
        const response = await fetch("addStation.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ station_name: stationName, city }),
        });
        const result = await response.json();
        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Update Successfully',
                text: 'Balance updated successfully.',
                timer: 2000,
                showConfirmButton: true
            });
            fetchStations();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: result.message
            });
        }
    } catch (error) {
        console.error("Error adding station:", error);
    }
})

// Show Popup
function showEditPopup(schedule) {
    const popup = document.getElementById("editTrainPopup");
    document.getElementById("editTrainName").value = schedule.Train_name;
    document.getElementById("editStationName").value = schedule.Station_name;
    document.getElementById("editDepartureTime").value = schedule.Departure_time;
    document.getElementById("editArrivalTime").value = schedule.Arrival_time;
    popup.classList.remove("hidden");
}

// Close Popup
document.getElementById("closePopup").addEventListener("click", () => {
    const popup = document.getElementById("editTrainPopup");
    popup.classList.add("hidden");
});

// Save Changes
document.getElementById("saveTrainChanges").addEventListener("click", async () => {
    const scheduleId = document.getElementById("editTrainPopup").dataset.scheduleId;
    const newTrainName = document.getElementById("editTrainName").value;
    const newStationName = document.getElementById("editStationName").value;
    const newDepartureTime = document.getElementById("editDepartureTime").value;
    const newArrivalTime = document.getElementById("editArrivalTime").value;

    try {
        const response = await fetch("updateTrainSchedule.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: scheduleId,
                Train_name: newTrainName,
                Station_name: newStationName,
                Departure_time: newDepartureTime,
                Arrival_time: newArrivalTime,
            }),
        });
        const result = await response.json();
        if (result.status === "success") {
            Swal.fire({
                icon: "success",
                title: "Update Successful",
                text: "Schedule updated successfully.",
                timer: 2000,
                showConfirmButton: false,
            });
            fetchTrainSchedule();
            document.getElementById("editTrainPopup").classList.add("hidden");
        } else {
            Swal.fire({
                icon: "error",
                title: "Update Failed",
                text: result.message,
            });
        }
    } catch (error) {
        console.error("Error updating schedule:", error);
    }
});

// Attach Edit Button Event
async function editSchedule(scheduleId) {
    try {
        const response = await fetch(`getTrainScheduleById.php?id=${scheduleId}`);
        const schedule = await response.json();
        if (schedule) {
            const popup = document.getElementById("editTrainPopup");
            popup.dataset.scheduleId = scheduleId;
            showEditPopup(schedule);
        }
    } catch (error) {
        console.error("Error fetching schedule:", error);
    }
}

// Sidebar Toggle and Initial Fetch


    // Fetch initial data
    fetchTrainSchedule();
    fetchUsers();
    fetchStations();
    flatpickr("#editArrivalTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", 
        time_24hr: true 
      });
    flatpickr("#editDepartureTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", 
        time_24hr: true 
      });