/* ═══════════════════════════════════════════
   KHANH BEAUTY - GLOBAL BOOKING WIZARD JS
   ═══════════════════════════════════════════ */

(function() {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    initBookingWizard();
  });

  function initBookingWizard() {
    const bookingForms = document.querySelectorAll('#bookingFormAjax'); // Use querySelectorAll in case of multiple (though IDs should be unique)
    
    bookingForms.forEach(form => {
      const container = form.closest('.kb-wizard');
      if (!container) return;

      const panes = container.querySelectorAll('.kb-wizard-pane');
      const stepIndicators = container.querySelectorAll('.kb-wizard-step-ind');
      const wizardLine = container.querySelector('#wizardLine');
      let currentStep = 1;

      // Elements
      const serviceSelect = container.querySelector('#service_id');
      const variantWrap = container.querySelector('#variantWrap');
      const variantSelect = container.querySelector('#variant_id');
      const staffGrid = container.querySelector('#staffGrid');
      const staffIdInput = container.querySelector('#staff_id');
      const bookingDate = container.querySelector('#booking_date');
      const bookingTime = container.querySelector('#booking_time');
      const btnNextStep2 = container.querySelector('#btnNextStep2');
      const btnNextStep3 = container.querySelector('#btnNextStep3');
      const bookingAlert = container.querySelector('#bookingAlert');

      // Chuyển UI step
      function updateWizardUI(step) {
        panes.forEach((pane, idx) => {
          if (idx + 1 === step) pane.classList.add('active');
          else pane.classList.remove('active');
        });

        if (step <= 4) {
          stepIndicators.forEach((ind, idx) => {
            if (idx + 1 === step) ind.classList.add('active');
            else ind.classList.remove('active');
          });
          
          if(wizardLine) {
            let percent = (step - 1) * 33.33;
            wizardLine.style.width = percent + '%';
          }
        }
      }

      // Step 1: Logic chọn dịch vụ & variant
      if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          const variantsData = selectedOption.getAttribute('data-variants');
          
          if (variantsData && variantsData !== "[]") {
            try {
              const variants = JSON.parse(variantsData);
              variantSelect.innerHTML = '<option value="">-- Chọn gói dịch vụ --</option>';
              variants.forEach(v => {
                variantSelect.innerHTML += `<option value="${v.id}">${v.variant_name} - ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v.price)}</option>`;
              });
              variantWrap.style.display = 'block';
            } catch(e) {
              variantWrap.style.display = 'none';
            }
          } else {
            variantWrap.style.display = 'none';
          }
        });
      }

      // Next & Prev
      container.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', () => {
          if (currentStep === 1) {
            if (!serviceSelect.value) return alert('Vui lòng chọn dịch vụ.');
            if (window.getComputedStyle(variantWrap).display !== 'none' && !variantSelect.value) return alert('Vui lòng chọn gói.');
            loadStaffs();
          }
          else if (currentStep === 2) {
            if (!staffIdInput.value) return alert('Vui lòng chọn chuyên gia.');
          }
          else if (currentStep === 3) {
            if (!bookingDate.value || !bookingTime.value) return alert('Vui lòng chọn ngày và giờ.');
          }

          currentStep++;
          updateWizardUI(currentStep);
        });
      });

      container.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => {
          if (currentStep > 1) {
            currentStep--;
            updateWizardUI(currentStep);
          }
        });
      });

      function loadStaffs() {
        if (!staffGrid) return;
        staffGrid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:20px;">Đang tải...</div>';
        fetch(`/booking/staffs?service_id=${serviceSelect.value}`)
          .then(r => r.json())
          .then(res => {
            staffGrid.innerHTML = '';
            if (res.success && res.data.length > 0) {
              res.data.forEach(staff => {
                const card = document.createElement('div');
                card.className = 'kb-staff-card';
                card.innerHTML = `
                  <div class="kb-staff-avatar">${staff.full_name.substring(0,2).toUpperCase()}</div>
                  <div class="kb-staff-name">${staff.full_name}</div>
                  <div class="kb-staff-role">Makeup Artist</div>
                `;
                card.addEventListener('click', () => {
                  container.querySelectorAll('.kb-staff-card').forEach(c => c.classList.remove('selected'));
                  card.classList.add('selected');
                  staffIdInput.value = staff.id;
                  btnNextStep2.disabled = false;
                });
                staffGrid.appendChild(card);
              });
            } else {
              staffGrid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:20px; color:red;">Không có chuyên gia phù hợp.</div>';
            }
          });
      }

      if (bookingDate) {
        bookingDate.addEventListener('change', () => {
          if (!bookingDate.value || !staffIdInput.value) return;
          bookingTime.innerHTML = '<option value="">Đang tải...</option>';
          bookingTime.disabled = true;
          fetch(`/booking/slots?date=${bookingDate.value}&staff_id=${staffIdInput.value}`)
            .then(r => r.json())
            .then(res => {
              bookingTime.innerHTML = '<option value="">-- Chọn khung giờ --</option>';
              if (res.success && res.data.length > 0) {
                res.data.forEach(t => bookingTime.innerHTML += `<option value="${t}">${t}</option>`);
                bookingTime.disabled = false;
              } else {
                bookingTime.innerHTML = '<option value="">Hết lịch rảnh</option>';
              }
            });
        });
      }

      if (bookingTime) {
        bookingTime.addEventListener('change', () => {
          if (bookingTime.value) btnNextStep3.disabled = false;
        });
      }

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btnSubmit = container.querySelector('#btnSubmit');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Đang xử lý...';
        
        fetch('/booking/submit', {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: new FormData(this)
        })
        .then(r => r.json())
        .then(res => {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = 'Xác Nhận Đặt Lịch';
          if (res.success) {
            container.querySelector('#res_code').textContent = res.booking_code;
            currentStep = 5;
            updateWizardUI(currentStep);
          } else {
            bookingAlert.textContent = res.message || 'Lỗi đặt lịch.';
            bookingAlert.classList.remove('hidden');
          }
        })
        .catch(() => {
          btnSubmit.disabled = false;
          bookingAlert.textContent = 'Mất kết nối server.';
          bookingAlert.classList.remove('hidden');
        });
      });
    });
  }

  // Export globally so modal can re-init if needed
  window.reinitBookingWizard = initBookingWizard;

})();
