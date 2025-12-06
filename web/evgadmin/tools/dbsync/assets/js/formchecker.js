var i, j, formchecker = {
	result: null, objects: [], object: null, nodeEl: null, errorMessage: '', foundError: false, fieldName: '', formValid: false,
	init: function (form, args) {
		args = args || {};
		if (args.scrollAdd) {
			form.data('scrollAdd', args.scrollAdd);
		}
		form.submit(function () {
			formchecker.result = formchecker.test(this, args.scroll ? args.scroll : true);
			this.formValid = formchecker.result;
			if (args.onError && !this.formValid) {
				args.onError(this);
			}
			if (args.onValid && this.formValid) {
				this.formValid = args.onValid({ target: this });
				this.formValid = this.formValid == true || this.formValid == false ? this.formValid : true;
			}
			return this.formValid;
		});
	},
	test: function (form, scroll) {
		var error;
		this.objects = $(form).find('input,select,textarea');
		this.foundError = false;
		for (i = 0; i < this.objects.length; i++) {
			this.object = this.objects[i];
			this.object.onfocus = function () {
				formchecker.hideError(this);
			};
			error = !this.checkInput(this.object);
			if (this.foundError == false && error) {
				this.foundError = true;
			}
		}
		if (this.foundError) {
			var
				errorItem = $('.formchecker-error'),
				scrollAdd,
				scrollPos;

			if (errorItem.length > 0 && scroll) {
				scrollAdd = errorItem.parents('form').data('scrollAdd');
				errorItem = errorItem[0];
				scrollPos = $(errorItem.parentNode).find('input, textarea, select').offset().top - 10;
				if (scrollAdd) {
					scrollPos += scrollAdd * 1;
				}
				$('html,body').animate({
					scrollTop: scrollPos
				});
			}
		}
		return this.foundError == false;
	},
	filters: {
		length: function (data, len) {
			return data.trim().length >= len;
		},
		number: function (num) {
			return !isNaN(Number(num)) && num != '';
		},
		e_mail: function (addr) {
			return (/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/).test(addr);
		},
		evalFunction: function (func, object) {
			return eval(func + '(object);') == true;
		},
		checkFormat: function (name, list) {
			return list != '' && new RegExp('\\.(' + list.replace(/\,/g, '|') + ')$').test(name);
		}
	},
	hideError: function (obj) {
		this.nodeEl = obj.parentNode.getElementsByClassName('error')[0];
		if (this.nodeEl) {
			this.nodeEl.parentNode.removeChild(this.nodeEl);
		}
	},
	showError: function (obj, message) {
		this.nodeEl = Array.prototype.slice.call(obj.parentNode.getElementsByClassName('error')).filter(function (htEl) {
			return htEl.errObj == obj;
		});
		this.nodeEl = this.nodeEl[0];
		if (message == '') {
			this.nodeEl ? this.nodeEl.parentNode.removeChild(this.nodeEl) : 0;
		} else {
			if (!this.nodeEl) {
				this.nodeEl = document.createElement('div');
				this.nodeEl.className = 'error formchecker-error';
				this.nodeEl.style.color = '#f00';
				this.nodeEl.style.textAlign = 'left';
				this.nodeEl.style.marginTop = '5px';
				this.nodeEl.errObj = obj;
				obj.parentNode.appendChild(this.nodeEl);
			}
			this.nodeEl.innerHTML = '<span class="arrow"></span>' + message;
		}
	},
	checkInput: function (element) {
		var
			errorMessage = '',
			inpVal = element.value.trim();

		if (element.d('type') && element.disabled == false) {
			this.fieldName = 'this';
			if (element.d('label')) {
				this.fieldName = element.d('label');
			} else if (element.placeholder) {
				this.fieldName = element.placeholder.toLowerCase();
			} else if (element.name != '') {
				this.fieldName = element.name.replace(/[^0-9A-Za-z]/gi, ' ');
			}
			errorMessage = '';
			if (element.d('type') == 'string') {
				j = element.d('minlen') || 1;
				if (this.filters.length(inpVal, j) == false) {
					errorMessage = j <= 1 ? 'invalid ' + this.fieldName + '.' : this.fieldName + ' requires minimum ' + j + ' letters !';
					this.foundError = true;
				}
			} else if (element.d('type') == 'number') {
				if (!(element.d('allowempty') && inpVal === '')) {
					if (element.d('allowed')) {
						element.value = inpVal.replace(new RegExp(element.d('allowed'), 'gi'), '');
					}
					if (this.filters.number(inpVal) == false) {
						errorMessage = 'invalid ' + this.fieldName + '.';
					} else {
						var
							amtmin = element.d('min'),
							amtmax = element.d('max');

						amtmin = amtmin ? amtmin : NaN;
						amtmax = amtmax ? amtmin : NaN;

						var
							nInpVal = Number(inpVal),
							nAmtMin = Number(amtmin),
							nAmtMax = Number(amtmax),
							amterr = '';

						amterr = !isNaN(nAmtMin) && nInpVal < nAmtMin ? 'minimum ' + amtmin : amterr;
						amterr += !isNaN(nAmtMax) && nInpVal > nAmtMax ? (amterr != '' ? ' and ' : '') + 'maximum ' + amtmax : '';
						errorMessage = (amterr != '' ? this.fieldName + ' should be ' : '') + amterr;
					}
				}

			} else if (element.d('type') == 'email') {
				if (this.filters.e_mail(inpVal) == false) {
					errorMessage = 'invalid ' + this.fieldName + '.';
				}

			} else if (element.d('type') == 'check') {
				if (!element.checked) {
					errorMessage = 'please check ' + this.fieldName + '.';
				}

			} else if (element.d('type') == 'func' && element.d('func')) {
				if (this.filters.evalFunction(element.d('func'), element) == false) {
					errorMessage = 'invalid ' + this.fieldName + '.';
				}
			} else if (element.d('type') == 'files' && element.d('extensions')) {
				if (this.filters.checkFormat(inpVal, element.d('extensions')) == false) {
					errorMessage = 'invalid ' + this.fieldName + '.';
				}
			}
			errorMessage = errorMessage != '' && element.d('errormsg') ? element.d('errormsg') : errorMessage;
			this.showError(element, errorMessage);
		}
		return errorMessage == '';
	}
};
$.fn.formchecker = function (attrs) {
	attrs = attrs || {};
	formchecker.init(this, attrs);
	return $(this);
};