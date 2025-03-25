// Define months and weekdays
const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

// Function to render the calendar
function updateCalendar() {
    document.getElementById('current-month').innerText = `${months[currentMonth]} ${currentYear}`;
    generateCalendar(events, birthdays, currentMonth, currentYear);
}

// Function to generate the calendar for the selected month and year
function generateCalendar(events, birthdays, month, year) {
    const calendar = document.getElementById('calendar');
    const daysInMonth = new Date(year, month + 1, 0).getDate(); // Total days in current month
    const firstDay = new Date(year, month, 1).getDay(); // First day of the month

    let calendarHTML = `<h3>${months[month]} ${year}</h3><table><thead><tr>`;

    daysOfWeek.forEach(day => {
        calendarHTML += `<th>${day}</th>`;
    });
    calendarHTML += `</tr></thead><tbody><tr>`;

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += `<td></td>`;
    }

    // Add the days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        if ((firstDay + day - 1) % 7 === 0 && day !== 1) {
            calendarHTML += `</tr><tr>`;
        }

        let dayHTML = `<td>${day}`;

        // Add events for this day
        events.forEach(event => {
            const eventDate = new Date(event.event_date);
            if (eventDate.getDate() === day && eventDate.getMonth() === month && eventDate.getFullYear() === year) {
                dayHTML += `<div class="event" onclick="openEventModal(${JSON.stringify(event)})">${event.event_title}</div>`;
            }
        });

        // Add birthdays for this day
        birthdays.forEach(user => {
            const birthday = new Date(user.date_of_birth);
            if (birthday.getDate() === day && birthday.getMonth() === month) {
                dayHTML += `<div class="birthday">${user.first_name}'s Birthday</div>`;
            }
        });

        dayHTML += `</td>`;
        calendarHTML += dayHTML;
    }

    calendarHTML += `</tr></tbody></table>`;
    calendar.innerHTML = calendarHTML;
}

// Function to open the event modal with event details
function openEventModal(event) {
    document.getElementById('event-title').innerText = event.event_title;
    document.getElementById('event-description').innerText = event.event_description;
    document.getElementById('event-date').innerText = event.event_date;
    document.getElementById('event-time').innerText = event.event_time;
    document.getElementById('event-creator').innerText = event.created_by;

    // Show the modal
    document.getElementById('event-modal').style.display = 'block';
}

// Close the modal when the close button is clicked
document.getElementById('close-modal').addEventListener('click', function() {
    document.getElementById('event-modal').style.display = 'none';
});

// Close the modal when clicking outside of it
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('event-modal')) {
        document.getElementById('event-modal').style.display = 'none';
    }
});

// Calendar navigation (prev/next month)
document.getElementById('prev-month').addEventListener('click', function() {
    if (currentMonth === 0) {
        currentMonth = 11;
        currentYear--;
    } else {
        currentMonth--;
    }
    updateCalendar();
});

document.getElementById('next-month').addEventListener('click', function() {
    if (currentMonth === 11) {
        currentMonth = 0;
        currentYear++;
    } else {
        currentMonth++;
    }
    updateCalendar();
});

// Initial calendar load
document.addEventListener('DOMContentLoaded', function() {
    updateCalendar();
});
