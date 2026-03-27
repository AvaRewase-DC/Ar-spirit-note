@php
    use Carbon\Carbon;
    Carbon::setLocale('ar');
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب حضور خدمة</title>
    <style>
        :root {--ken-primary:#ad1100; --ken-bg:#f4f4f4; --ken-text:#353535; --ken-muted:#7c7c7c;}
        body {font-family: Tahoma, sans-serif; background: var(--ken-bg); margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 14px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .title {text-align: center; margin: 0;}
        .subtitle {text-align: center; color: var(--ken-muted); margin-top: 6px;}
        .label {font-weight: bold; margin-bottom: 10px; display: block;}
        .inline {display: flex; gap: 8px; align-items: center; flex-wrap: nowrap;}
        .inline input[type="tel"] {flex: 1; min-width: 0; width: auto;}
        .inline span {flex-shrink: 0; white-space: nowrap;}
        input[type="text"], input[type="tel"], select {width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;}
        .btn {background: var(--ken-primary); color: #fff; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; width: 100%;}
        .help {font-size: 13px; color: var(--ken-muted); margin-top: 8px;}
        /* validation */
        .field-error {font-size:13px; color:#a00; margin-top:5px; display:none;}
        .input-invalid {border-color:#a00 !important; box-shadow:0 0 0 2px rgba(160,0,0,.12) !important;}
        .error {background: #ffe9e9; color: #a00; border: 1px solid #f2b9b9; padding: 10px; border-radius: 6px; margin-bottom: 10px;}
        .id-derived {display: flex; gap: 12px; margin-top: 12px;}
        .id-derived .field {flex:1;}
        .id-derived label {font-size: 12px; color: var(--ken-muted); display:block; margin-bottom:4px;}
        .id-derived input[type="text"] {background:#f4f4f4; color:var(--ken-text); font-size:14px; cursor:default; pointer-events:none;}
        /* popup */
        .popup-overlay {display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1200; align-items:center; justify-content:center;}
        .popup-overlay.open {display:flex;}
        .popup {background:#fff; border-radius:8px; padding:18px 20px; max-width:420px; width:94%; box-shadow:0 8px 30px rgba(0,0,0,.25); text-align:center;}
        .popup.success .title {color: #0a7a2f;}
        .popup.error .title {color: #a00;}
        .popup .msg {margin-top:8px; color:#333;}
        .popup .actions {margin-top:14px; display:flex; gap:8px; justify-content:center}
        .popup .btn {min-width:120px}
    </style>
</head>
<body>
    <div class="container">
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('mass.request.store') }}">
            @csrf

            <div class="card">
                <h2 class="title">طلب حجز خدمة</h2>
            </div>

            <div class="card">
                <span class="label"> الخدمة</span>
                <select name="massAppointmentId" required>
                    <option value="">اختر الخدمة</option>
                    @foreach ($appointments as $item)
                        @php
                            $appointmentDate = data_get($item, 'appointmentDate');
                            $dateText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('dddd D MMMM YYYY') : '';
                            $timeText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('h:mm a') : '';
                        @endphp
                        <option value="{{ data_get($item, 'id') }}" @selected(old('massAppointmentId') == data_get($item, 'id'))>
                            {{ $dateText }} - {{ data_get($item, 'title') }} - {{ $timeText }} - {{ data_get($item, 'place') }} - الاماكن المتاحة: {{ data_get($item, 'availableSeats') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="card">
                <span class="label" style="color: var(--ken-primary);"> بياناتك الشخصية</span>
                <div class="inline" dir="ltr">
                    <span>E1C1F</span>
                    <input id="familyNumber" type="tel" maxlength="5" name="familyNumber" value="{{ old('familyNumber') }}" required autocomplete="off" placeholder="-----">
                    <span>NR</span>
                    <input id="familyMemberCode" type="tel" maxlength="2" name="familyMemberCode" value="{{ old('familyMemberCode') }}" required autocomplete="off" placeholder="--">
                    <span>رقم العضوية</span>
                </div>
            </div>

            <div class="card">
                <span class="label" style="color: var(--ken-primary);">الاسم بالكامل</span>
                <input id="memberName" type="text" name="memberName" value="{{ old('memberName') }}" required>
                <div class="field-error" id="nameError">الاسم يجب أن يحتوي على جزءين على الأقل (الاسم الأول واسم العائلة)</div>
            </div>

            <div class="card">
                <span class="label" style="color: var(--ken-primary);">الرقم القومي</span>
                <input id="nationalId" type="tel" name="nationalId" maxlength="14" minlength="14" value="{{ old('nationalId') }}" placeholder="الرقم القومي (14 رقم)" required autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <div class="field-error" id="nationalIdError">الرقم القومي يجب أن يكون 14 رقم ويبدأ بـ 2 أو 3</div>
                <p class="help">أدخل الرقم القومي الخاص بالأب أو الأم للأعضاء الأقل من 16 سنة الغير معروف الرقم القومي الخاص بهم</p>

                {{-- auto-derived read-only display - always visible --}}
                <div class="id-derived" id="idDerivedWrap">
                    <div class="field">
                        <label>تاريخ الميلاد</label>
                        <input type="text" id="birthDateDisplay" readonly tabindex="-1" placeholder="يتم حسابه تلقائياً">
                    </div>
                    <div class="field">
                        <label>النوع</label>
                        <input type="text" id="genderDisplay" readonly tabindex="-1" placeholder="يتم تحديده تلقائياً">
                    </div>
                </div>

                {{-- hidden inputs sent to server --}}
                <input type="hidden" name="birthDate" id="birthDateInput" value="{{ old('birthDate') }}">
                <input type="hidden" name="gender"    id="genderInput"    value="{{ old('gender') }}">
            </div>

            <div class="card">
                <span class="label" style="color: var(--ken-primary);">رقم الموبايل</span>
                    <input id="mobileInput" type="tel" name="mobile" maxlength="11" minlength="11" value="{{ old('mobile') }}" placeholder="رقم الموبايل (11 رقم)" required autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <div class="field-error" id="mobileError">رقم الموبايل يجب أن يكون 11 رقم</div>
            </div>

            <button class="btn" type="submit">حجز الخدمة</button>
        </form>
    </div>

    <script>
        // ── Auto-derive birthDate & gender from Egyptian national ID ────────
        (function () {
            const nationalIdInput  = document.getElementById('nationalId');
            const birthDateInput   = document.getElementById('birthDateInput');
            const genderInput      = document.getElementById('genderInput');
            const birthDateDisplay = document.getElementById('birthDateDisplay');
            const genderDisplay    = document.getElementById('genderDisplay');
            const derivedWrap      = document.getElementById('idDerivedWrap');
            const nidError         = document.getElementById('nationalIdError');

            if (!nationalIdInput) return;

            function handleEgyptianId() {
                const digits = nationalIdInput.value.replace(/\D/g, '').split('');

                if (digits.length !== 14) {
                    birthDateInput.value = genderInput.value = '';
                    birthDateDisplay.value = genderDisplay.value = '';
                    return;
                }

                const century   = digits[0] === '2' ? '19' : '20';
                const birthDate = `${century}${digits[1]}${digits[2]}-${digits[3]}${digits[4]}-${digits[5]}${digits[6]}`;
                const gender    = Number(digits[12]) % 2 === 0 ? '1' : '0';

                birthDateInput.value   = birthDate;
                genderInput.value      = gender;
                birthDateDisplay.value = birthDate;
                genderDisplay.value    = gender === '0' ? 'ذكر' : 'أنثى';
            }

            nationalIdInput.addEventListener('input', function () {
                // Strip any non-digit characters
                const clean = nationalIdInput.value.replace(/\D/g, '');
                if (clean !== nationalIdInput.value) {
                    nationalIdInput.value = clean;
                }
                handleEgyptianId();
            });
            if (nationalIdInput.value.length === 14) handleEgyptianId();
        })();

        // ── Client-side form validation ─────────────────────────────────────
        (function () {
            const form        = document.querySelector('form');
            const nameInput   = document.getElementById('memberName');
            const nidInput    = document.getElementById('nationalId');
            const mobileInput = document.getElementById('mobileInput');
            const nameErr     = document.getElementById('nameError');
            const nidErr      = document.getElementById('nationalIdError');
            const mobileErr   = document.getElementById('mobileError');

            function showErr(input, errEl, show) {
                if (show) {
                    input.classList.add('input-invalid');
                    errEl.style.display = 'block';
                } else {
                    input.classList.remove('input-invalid');
                    errEl.style.display = 'none';
                }
            }

            // Live validation
            if (nameInput) {
                nameInput.addEventListener('blur', function () {
                    const parts = nameInput.value.trim().split(/\s+/).filter(Boolean);
                    showErr(nameInput, nameErr, parts.length < 2);
                });
            }

            if (nidInput) {
                nidInput.addEventListener('input', function () {
                    const v = nidInput.value.replace(/\D/g, '');
                    const ok = v.length === 14 && /^[23]/.test(v);
                    showErr(nidInput, nidErr, nidInput.value.length > 0 && !ok);
                });
            }

            if (mobileInput) {
                mobileInput.addEventListener('input', function () {
                    // enforce digits only
                    const clean = mobileInput.value.replace(/\D/g, '');
                    if (clean !== mobileInput.value) mobileInput.value = clean;
                    const ok = clean.length === 11 && /^(010|011|012)[0-9]{8}$/.test(clean);
                    showErr(mobileInput, mobileErr, mobileInput.value.length > 0 && !ok);
                });
            }

            // Submit guard
            if (form) {
                form.addEventListener('submit', function (e) {
                    let valid = true;

                    const parts = nameInput ? nameInput.value.trim().split(/\s+/).filter(Boolean) : [];
                    if (nameInput && parts.length < 2) {
                        showErr(nameInput, nameErr, true);
                        valid = false;
                    }

                    if (nidInput) {
                        const v = nidInput.value.replace(/\D/g, '');
                        if (v.length !== 14 || !/^[23]/.test(v)) {
                            showErr(nidInput, nidErr, true);
                            valid = false;
                        }
                    }

                    if (mobileInput) {
                        const v = mobileInput.value.replace(/\D/g, '');
                        if (v.length !== 11 || !/^01[012]/.test(v)) {
                            showErr(mobileInput, mobileErr, true);
                            valid = false;
                        }
                    }

                            if (!valid) {
                                    e.preventDefault();
                                    // scroll to first error
                                    const firstErr = form.querySelector('.input-invalid');
                                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                });
            }
        })();
                </script>

                <!-- Popup modal for submission result -->
                <div id="submitPopupOverlay" class="popup-overlay" aria-hidden="true">
                    <div id="submitPopup" class="popup" role="dialog" aria-modal="true">
                        <div class="title" id="submitPopupTitle">تم</div>
                        <div class="msg" id="submitPopupMsg">تم إرسال الطلب بنجاح.</div>
                        <div class="actions">
                            <button id="submitPopupClose" class="btn">حسناً</button>
                        </div>
                    </div>
                </div>

                <script>
                    (function () {
                        const form = document.querySelector('form');
                        const overlay = document.getElementById('submitPopupOverlay');
                        const popup = document.getElementById('submitPopup');
                        const title = document.getElementById('submitPopupTitle');
                        const msg = document.getElementById('submitPopupMsg');
                        const closeBtn = document.getElementById('submitPopupClose');

                        function showPopup(type, text) {
                            popup.classList.remove('success', 'error');
                            popup.classList.add(type);
                            title.textContent = type === 'success' ? 'نجاح' : 'خطأ';
                            msg.textContent = text || (type === 'success' ? 'تم إرسال الطلب بنجاح.' : 'حدث خطأ، حاول مرة أخرى.');
                            overlay.classList.add('open');
                            overlay.setAttribute('aria-hidden', 'false');
                        }

                        function hidePopup() {
                            overlay.classList.remove('open');
                            overlay.setAttribute('aria-hidden', 'true');
                        }

                        closeBtn.addEventListener('click', hidePopup);

                        if (!form) return;

                        form.addEventListener('submit', function (e) {
                            e.preventDefault();

                            // Disable native submit while we do AJAX
                            const submitBtn = form.querySelector('[type="submit"]');
                            if (submitBtn) submitBtn.disabled = true;

                            const fd = new FormData(form);

                            fetch(form.action, {
                                method: form.method || 'POST',
                                body: fd,
                                credentials: 'same-origin',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            }).then(async function (res) {
                                if (res.status === 422) {
                                    const data = await res.json().catch(() => ({}));
                                    // collect first validation message
                                    const first = data?.errors ? Object.values(data.errors).flat()[0] : 'بيانات غير صحيحة.';
                                    showPopup('error', first || 'بيانات غير صحيحة.');
                                    if (submitBtn) submitBtn.disabled = false;
                                    return;
                                }

                                if (!res.ok) {
                                    // Try parse message
                                    let text = 'حدث خطأ، حاول مرة أخرى.';
                                    try { text = await res.text(); } catch (e) {}
                                    showPopup('error', text);
                                    if (submitBtn) submitBtn.disabled = false;
                                    return;
                                }

                                // success — show success popup then redirect to response URL or reload
                                showPopup('success', 'تم إرسال الطلب بنجاح. جارٍ التحويل...');
                                setTimeout(function () {
                                    // If response redirected to another page, follow it; otherwise reload
                                    if (res.redirected && res.url) {
                                        window.location.href = res.url;
                                    } else {
                                        // try to go to a done page if exists
                                        try { window.location.href = '{{ route('mass.request.done') }}'; } catch (e) { window.location.reload(); }
                                    }
                                }, 900);
                            }).catch(function (err) {
                                showPopup('error', 'حدث خطأ في الشبكة.');
                                if (submitBtn) submitBtn.disabled = false;
                            });
                        });
                    })();
    </script>
</body>
</html>
