<!-- Static navbar -->
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{!! route('home') !!}">DYNAX</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav menu">
                <li><a href="{!! url('f01') !!}">日報</a></li>
                <li><a href="{!! url('f02') !!}">予定</a></li>
                <li><a href="{!! url('f03') !!}">検索</a></li>
                @if($user->isAdmin())
                <li><a href="{!! url('f04') !!}">データ取込</a></li>
                <li><a href="{!! url('f05') !!}">社員情報</a></li>
                @endif
                <li class="hidden"><a href="{!! url('f06') !!}">マスタ管理</a></li>
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#">
                        @if($user->isAdmin())
                        <span class="badge">Admin</span>
                        @endif 
                        {!! $user->name !!} [{!! $user->id !!}]
                        </a>
                    </li>
                    <li><a href="{!! url('auth/logout') !!}">ログアウト</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>