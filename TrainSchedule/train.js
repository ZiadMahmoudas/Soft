document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.getElementById("train-schedule");

    // إضافة مؤشر تحميل
    tbody.innerHTML = `<tr><td colspan="4">Loading train schedules...</td></tr>`;

    try {
        // جلب بيانات جدول القطارات
        const response = await fetch("exampleUsage.php");
        if (!response.ok) throw new Error("Failed to fetch train schedule");
        const trainSchedule = await response.json();

        // التحقق من وجود بيانات
        if (trainSchedule.length > 0) {
            tbody.innerHTML = ""; // مسح رسالة التحميل
            trainSchedule.forEach((train) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${train.Train_name}</td>
                    <td>${train.Station_name}</td>
                    <td>${train.Departure_time}</td>
                    <td>${train.Arrival_time}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4">No train schedules available.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
        tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Error loading train schedules.</td></tr>`;
    }
});
