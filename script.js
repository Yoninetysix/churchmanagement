function showRegisterForm(role) {
    console.log("Button clicked for role: " + role); 

    let formHTML = '';
    
    if (role === 'member') {
        formHTML = `
            <h2>Member Registration</h2>
            <form action="submit_form.php" method="POST">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="date" name="dob" placeholder="Date of Birth" required>
                <input type="text" name="ministry" placeholder="Ministry" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="tel" name="mobile" placeholder="Mobile Number" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
        `;
    }

    if (role === 'pastor') {
        formHTML = `
            <h2>Pastor Registration</h2>
            <form action="submit_form.php" method="POST">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="date" name="dob" placeholder="Date of Birth" required>
                <input type="text" name="ministry" placeholder="Ministry" required>
                <select name="role" required>
                    <option value="Senior Pastor">Senior Pastor</option>
                    <option value="Associate Pastor">Associate Pastor</option>
                    <option value="Assistant Pastor">Assistant Pastor</option>
                </select>
                <input type="text" name="education" placeholder="Education" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="tel" name="mobile" placeholder="Mobile Number" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
        `;
    }

    // Add similar code for 'accountant', 'board_member', 'admin'...

    // Log what form HTML will be added
    console.log("Generated form HTML:", formHTML);

    // Dynamically inject the form HTML into the form container
    document.getElementById('formContainer').innerHTML = formHTML;
}
