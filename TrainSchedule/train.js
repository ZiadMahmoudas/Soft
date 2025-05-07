document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.getElementById("train-schedule");
    const userInfo = document.getElementById("user-info");

    // Add loading indicators
    tbody.innerHTML = `<tr><td colspan="5">Loading train schedules...</td></tr>`;
    userInfo.innerHTML = `<p>Loading user information...</p>`;

    try {
        // Fetch train schedule
        const scheduleResponse = await fetch("getTrainSchedule.php");
        if (!scheduleResponse.ok) throw new Error("Failed to fetch train schedule");
        const trainSchedule = await scheduleResponse.json();

        // Fetch logged-in user data
        const userResponse = await fetch("getUserData.php");
        if (!userResponse.ok) throw new Error("Failed to fetch user data");
        const userData = await userResponse.json();

        // Display train schedule
        if (trainSchedule.length > 0) {
            tbody.innerHTML = ""; // Clear loading message
            trainSchedule.forEach((train) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${train.train_name}</td>
                    <td>${new Date(train.departure_time).toLocaleString()}</td>
                    <td>${train.duration}</td>
                    <td>${new Date(train.arrival_time).toLocaleString()}</td>
                    <td>${train.price}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="5">No train schedules available.</td></tr>`;
        }

        // Display user information
        if (userData && !userData.error) {
            userInfo.innerHTML = `
                <p>Welcome, ${userData.name}</p>
                <p>Address: ${userData.address}</p>
                <p>Balance: ${userData.balance}</p>
            `;
        } else {
            userInfo.innerHTML = `<p class="text-danger">Error fetching user data: ${userData?.error || "Unknown error"}</p>`;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
        tbody.innerHTML = `<tr><td colspan="5" class="text-danger">Error loading train schedules.</td></tr>`;
        userInfo.innerHTML = `<p class="text-danger">Error loading user information.</p>`;
    }
});
