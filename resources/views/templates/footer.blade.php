</section>

@if (!empty($logged))
	<footer class="footer">
		<div class="wrapper">
			<div class="footer__left">
				<a href="{{ url('/') }}">
					<img src="{{ URL::asset('img/logo.png') }}" alt="logo" class="footer__logo logo">
				</a>
	
				<div class="footer__block">
					<div class="footer__name">Neosvita</div>
					<div class="footer__copyright">2019 &copy; Всі права захищені</div>
				</div>
			</div>
			<div class="footer__right">
				<a href="{{ url('/') }}" class="footer__link">НА ГОЛОВНУ</a>
			</div>
	
		</div>
	</footer>
@endif

<script src="{{ URL::asset('js/cleave.min.js') }}"></script>
<script src="{{ URL::asset('js/index.min.js') }}"></script>

</body>

</html>
