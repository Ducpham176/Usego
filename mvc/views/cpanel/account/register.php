<div class="block-top"></div>
<div class="container-logind" style="background: url('<?= _TEMPLATE . 'images/background/sign_background.png' ?>')">
    <header>
        <div class="header-account">
        <div class="loading-effect-top"></div>
            <div class="left">
                <a href="/usego/home">
                    <img src="<?= _TEMPLATE . 'images/icons/usego-hi.png' ?>" width="200">
                </a>
                <span>Đăng ký</span>
            </div>
            <div class="right">
                <a href="#" class="btn-needHelp" title="Giúp đỡ">Bạn cần giúp đỡ ?</a>
                <div class="collect-tips">
                    <em><img class="arrow-item" src="<?= _TEMPLATE . 'images/icons/cute-arrow-png.png' ?>"></em>
                    <span>Nhấn vào đây, Nếu bạn quên mật khẩu?</span>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="main-account">
            <form action="" method="POST" class="form-account">
                <div class="form-groups">
                    <div class="form-control">
                        <span>Tên của bạn </span>
                        <input 
                        type="text" 
                        name="firstname" 
                        class="input-field data-On on-interact" 
                        maxlength="25" 
                        placeholder="Tên người dùng"
                        autofocus
                        required="">
                    </div>
                    <div class="form-control">
                        <span>Họ của bạn </span>
                        <input 
                        type="text" 
                        name="lastname" 
                        class="input-field data-On on-interact" 
                        maxlength="25" 
                        placeholder="Họ người dùng"
                        required="">
                    </div>
                </div>
                <div class="form-groups">
                    <div class="form-control">
                        <span>Đăng ký với email</span>
                        <input 
                        type="text" 
                        name="email" 
                        class="input-field data-On on-interact" 
                        placeholder="Địa chỉ email"
                        required="">
                    </div>
                </div>
                <div class="form-groups">
                    <div class="form-control">
                        <input 
                        type="password" 
                        name="password" 
                        class="input-field data-On on-interact" 
                        placeholder="Mật khẩu"
                        autocomplete="current-password"
                        required="">
                        <small>Gợi ý: Mật khẩu cần ít nhất 8 kí tự</small>
                    </div>
                </div>
                <div class="form-groups">
                    <div class="form-control">
                        <input 
                        type="text" 
                        name="otp" 
                        class="input-field not-interact" 
                        maxlength="6" 
                        placeholder="Nhập mã xác nhận" readonly
                        required="">
                        <button class="btn-otp FormInput_disabled" disabled>Gửi mã</button>
                        <button class="btn-accuracy">Xác nhận</button>
                    </div>
                </div>
                <div class="form-groups">
                    <div class="form-control">
                        <button type="submit" id="btn-register" class="btn-gradient" title="Đăng ký">Đăng ký</button>
                    </div>
                </div>
                <div class="form-groups">
                    <div class="form-control">
                        <div class="form-text-center">
                            <span class="text-content-span">Bạn đã có tài khoản?</span><a href="/usego/account/logind">Đăng nhập</a> 
                        </div>
                    </div>
                </div>
                <div class="form-groups">
                    <div class="form-control">
                        <div class="form-text-center rules-text">
                            <span class="text-content-span"> Việc bạn tiếp tục sử dụng trang web này đồng nghĩa bạn đồng ý với điều khoản sử dụng của chúng tôi</span>
                        </div>
                    </div>
                </div>
            </form> 
            <!-- Loading -->
            <div class="loading-ss">
                 <img src="<?= _TEMPLATE . 'images/icons/circle-Loading.gif' ?>" width="40">
            </div>
        </div>
    </main>
</div>

<div class="container-forgot-password">
    <form action="" method="POST" class="form-account" id="form-forgot-password">
        <div class="form-item-reset">
            <button class="btnReset-forgot-password">
                <i class="fa-solid fa-rotate-right"></i> <span>Làm mới</span>
            </button>
        </div>
        <ul class="switch-items">
            <li class="item-swit item-accuracy-email show">
                Xác thực
            </li>
            <li class="item-swit item-accuracy-otp">
                <i class='bx bxs-lock'></i> OTP                
            </li>
        </ul>
        <div class="form-groups">
            <div class="form-control form-item-forgot">
                <input 
                type="text" 
                name="emailForgot" 
                class="input-field on-interact" 
                placeholder="Email đã đăng ký"
                required="">

                <input 
                type="text" 
                name="otpForgot" 
                class="input-field on-interact hidden" 
                placeholder="Mã OTP" 
                maxlength="6"
                required="">

                <input 
                type="password" 
                name="passwordForgot" 
                class="input-field on-interact hidden" 
                placeholder="Mật khẩu mới"
                autocomplete="current-password"
                required="">
                <!-- <span class="clock-time-delay">10s</span> -->
            </div>
        </div>
        <div class="form-groups">
            <div class="form-control">
                <!-- Google reCAPTCHA box -->
                <div class="g-recaptcha" id="g-groups-captcha" data-sitekey="6LdmPnEoAAAAAHIE_xD5SKuAciSzLQ2hPGT776tH"></div>
            </div>
        </div>
        <div class="form-groups">
            <div class="form-control">
                <button type="submit" class="btn-forgot-password" title="Xác nhận">Xác nhận</button>
                <button type="submit" class="btn-forgot-password" id="btn-submit-forgot" title="Xác nhận mật khẩu">Đổi mật khẩu</button>
            </div>
        </div>
    </form>
</div>
