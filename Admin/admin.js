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
                    <button class="btn btn-danger btn-sm delete-user" data-id="${user.User_id}">Delete</button>
                </td>
            `;
            userTable.appendChild(row);
        });

        // Attach event listeners for saving balances and deleting users
        document.querySelectorAll(".save-balance").forEach((button) => {
            button.addEventListener("click", () => saveBalance(button.dataset.id));
        });
        document.querySelectorAll(".delete-user").forEach((button) => {
            button.addEventListener("click", () => deleteUser(button.dataset.id));
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

// Delete User
async function deleteUser(userId) {
    console.log("Attempting to delete user with ID:", userId); // Debugging log

    const confirmation = await Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: 'This action will permanently delete the user.',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    });

    if (!confirmation.isConfirmed) {
        return;
    }

    try {
        const response = await fetch("admin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete_user", user_id: userId }),
        });
        const result = await response.json();
        console.log("Response from server:", result); // Debugging log
        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Deleted Successfully',
                text: 'User deleted successfully.',
                timer: 2000,
                showConfirmButton: true
            });
            fetchUsers(); // Refresh the user list
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Deletion Failed',
                text: result.message
            });
        }
    } catch (error) {
        console.error("Error deleting user:", error);
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
                <td>${station.city || 'N/A'}</td> <!-- Default to 'N/A' if city is undefined -->
                <td>
                    <button class="btn btn-warning btn-sm edit-station" data-id="${station.station_id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-station" data-id="${station.station_id}">Delete</button>
                </td>
            `;
            stationTable.appendChild(row);
        });

        // Attach event listeners for editing and deleting stations
        document.querySelectorAll(".edit-station").forEach((button) => {
            button.addEventListener("click", async () => {
                const stationId = button.dataset.id;
                const response = await fetch(`getStationById.php?id=${stationId}`);
                const station = await response.json();
                showEditStationPopup(station);
            });
        });

        document.querySelectorAll(".delete-station").forEach((button) => {
            button.addEventListener("click", () => deleteStation(button.dataset.id));
        });
    } catch (error) {
        console.error("Error fetching stations:", error);
    }
}

// Add Station
document.getElementById("addStationBtn").addEventListener("click", async () => {
    const stationName = document.getElementById("stationName1").value.trim();
    const city = document.getElementById("city").value.trim();

    if (!stationName || !city) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Fields',
            text: 'Please fill in all fields.',
        });
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
                title: 'Added Successfully',
                text: 'Station added successfully.',
                timer: 2000,
                showConfirmButton: true,
            });
            fetchStations();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Addition Failed',
                text: result.message,
            });
        }
    } catch (error) {
        console.error("Error adding station:", error);
    }
});

// Edit Station
function showEditStationPopup(station) {
    const popup = document.getElementById("editStationPopup");
    document.getElementById("editStationName").value = station.station_name || ''; // Default to empty string if undefined
    document.getElementById("editCity").value = station.city || ''; // Default to empty string if undefined
    popup.dataset.stationId = station.station_id; // Store station ID
    popup.classList.remove("hidden");
}

document.getElementById("saveStationChanges").addEventListener("click", async () => {
    const stationId = document.getElementById("editStationPopup").dataset.stationId;
    const newStationName = document.getElementById("editStationName").value.trim();
    const newCity = document.getElementById("editCity").value.trim();

    if (!stationId || !newStationName || !newCity) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Fields',
            text: 'Please fill in all fields.',
        });
        return;
    }

    try {
        const response = await fetch("updateStation.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ station_id: stationId, station_name: newStationName, city: newCity }),
        });
        const result = await response.json();
        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Updated Successfully',
                text: 'Station updated successfully.',
                timer: 2000,
                showConfirmButton: true,
            });
            fetchStations();
            document.getElementById("editStationPopup").classList.add("hidden");
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: result.message,
            });
        }
    } catch (error) {
        console.error("Error updating station:", error);
    }
});

document.getElementById("closeStationPopup").addEventListener("click", () => {
    document.getElementById("editStationPopup").classList.add("hidden");
});

// Delete Station
async function deleteStation(stationId) {
    const confirmation = await Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: 'This action will permanently delete the station.',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    });

    if (!confirmation.isConfirmed) {
        return;
    }

    try {
        const response = await fetch("deleteStation.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ station_id: stationId }),
        });
        const result = await response.json();
        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Deleted Successfully',
                text: 'Station deleted successfully.',
                timer: 2000,
                showConfirmButton: true,
            });
            fetchStations();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Deletion Failed',
                text: result.message,
            });
        }
    } catch (error) {
        console.error("Error deleting station:", error);
    }
}

// Show Popup
function showEditPopup(schedule) {
    const popup = document.getElementById("editTrainPopup");
    document.getElementById("editTrainName").value = schedule.Train_name;
    document.getElementById("editStationName").value = schedule.Station_name;
    document.getElementById("editDepartureTime").value = schedule.Departure_time;
    document.getElementById("editArrivalTime").value = schedule.Arrival_time;
    popup.dataset.scheduleId = schedule.id; // Store schedule ID
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
    const newTrainName = document.getElementById("editTrainName").value.trim();
    const newStationName = document.getElementById("editStationName").value.trim();
    const newDepartureTime = document.getElementById("editDepartureTime").value.trim();
    const newArrivalTime = document.getElementById("editArrivalTime").value.trim();

    if (!scheduleId || !newTrainName || !newStationName || !newDepartureTime || !newArrivalTime) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Fields',
            text: 'Please fill in all fields.',
        });
        return;
    }

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
            fetchTrainSchedule(); // Refresh the train schedule table
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
    flatpickr("#arrivalTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", 
        time_24hr: true 
      });
    flatpickr("#departureTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", 
        time_24hr: true 
      });