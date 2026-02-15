
        document.addEventListener('DOMContentLoaded', () => {
            function updateClock() {
              const now = new Date();
              const hours = now.getHours().toString().padStart(2, '0');
              const minutes = now.getMinutes().toString().padStart(2, '0');
              const seconds = now.getSeconds().toString().padStart(2, '0');
              document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        
              const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
              const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
              const dayOfWeek = days[now.getDay()];
              const day = now.getDate();
              const month = months[now.getMonth()];
              const year = now.getFullYear();
              document.getElementById('date').textContent = `${dayOfWeek}, ${month} ${day}, ${year}`;
            }
        
            // Update the clock and date every second
            setInterval(updateClock, 1000);
        
            // Initial update
            updateClock();
          });
        
        
         
        
        
        