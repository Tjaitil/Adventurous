            <h2 class="page_title"> Profile </h2>
            <div id="user_info">
                <form>
                    <label for="username"> Username </label>
                    <input value="<?php echo $_SESSION['username']?>" readonly/></br>
                    <label for="email"> Email </label>
                    <input value="<?php echo $this->data['email'];?>" readonly/></br>
                    <label for="country"> Country </label>
                    <input value="<?php echo $this->data['country'];?>" readonly/></br>
                    <label> Account created </label>
                    <input value="<?php echo $this->data['time_created'];?>" readonly/>
                </form>
            </div>
            <button onclick="show('password');"> Change Password </button>
            <div id="password">
                <p> This section is currently undergoing work and is not working at the moment</p>
                <form>
                    <label for="current_password"> Current Password: </label>
                    <input name="current_password" type="password" required /></br>
                    <label for="new_password"> New Password: </label>
                    <input name="new_password" type="password" required title="Please enter a new password" /></br>
                    <label for="confirm_password"> Confirm Password: </label>
                    <input name="confirm_password" type="password" required title="Please conifrm your password" />
                    <button type="button"> Change Password! </button> 
                </form>
            </div>