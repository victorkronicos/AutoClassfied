<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Anuncio;
use App\User;
use App\Revenda;
use App\VisualizacaoAnuncio;
use App\Contato;
use App\Endereco;
use App\UserDado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EmailConfirmation;
use Illuminate\Notifications\Notifiable;

class UserController extends AppBaseController
{

    use Notifiable;

    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        /*$this->userRepository->pushCriteria(new RequestCriteria($request));
        $users = $this->userRepository->paginate(20);*/
        $s = $request->input('s');
        $users = User::where('name', 'like', '%'.$s.'%')->orWhere('email', 'like', '%'.$s.'%')->paginate(20);
        return view('users.index')
            ->with('users', $users);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $user = $this->userRepository->create($input);

        Flash::success('User saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified User in storage.
     *
     * @param  int              $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = User::find($id);
        if(!empty($user)){
          if($user->id == Auth::user()->id){
            //$user = $this->userRepository->update($request->all(), $id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->documento = $request->input('documento');
            $user->pessoa_fisica = $request->input('pessoa_fisica');
            $user->save();
            $telefone = UserDado::where('nome', 'telefone')->first();
            $telefone = $telefone?$telefone:new UserDado();
            $telefone->nome = 'telefone';
            $telefone->valor = $request->input('telefone');
            $telefone->user = $user->id;
            $telefone->save();
            Flash::success('Usuário atualizado com sucesso!');
            return redirect()->back();
          }
          Flash::error('Você está tentando atualizar as informações alterando o código da página');
          return redirect()->back();
        }else{
          Flash::error('Usuário não encontrado');
          return redirect()->back();
        }
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

        Flash::success('User deleted successfully.');

        return redirect(route('users.index'));
    }

    public function profile(Request $request){
      $user = Auth::user();
      return view('user.home')->with(['user'=> $user]);
    }

    public function meus_anuncios(Request $request){
      $user = Auth::user();
      $anuncios = Anuncio::where('user', $user->id)->paginate(5);
      return view('user.meusanuncios')->with('anuncios', $anuncios);
    }

    public function fale_conosco(Request $request){
      return view('fale_conosco');
    }

    public function duvida_comprar_carro(Request $request){
      return view('duvidas.comprar_veiculo');
    }

    public function duvida_vender_carro(Request $request){
      return view('duvidas.vender_veiculo');
    }

    public function duvida_anuncios(Request $request){
      return view('duvidas.anuncios');
    }

    public function admin(Request $request){
      $anuncios = Anuncio::orderBy('visualizacoes', 'desc')->paginate(5);
      return view('admin.resumo')->with([
                                         'anuncios' => $anuncios,
                                         'usuarios_count'=> User::all()->count(),
                                         'anuncios_count'=> Anuncio::all()->count(),
                                         'anuncios_recentes'=> Anuncio::orderBy('id', 'desc')->paginate(10),
                                         'contatos'=> Contato::orderBy('id', 'desc')->paginate(10),
                                         'revendas_count'=> Revenda::all()->count(),
                                        ]);
    }

    public function tables(Request $request){
      return view('admin.tables');
    }

    public function form(Request $request){
      return view('admin.form');
    }

    public function configuracoes(Request $request){
      return view('user.configuracoes');
    }

    public function faq(Request $request){
      return view('duvidas.faq');
    }

    public function termos_uso(Request $request){
      return view('duvidas.termos_de_uso');
    }

    public function cadastrarEndereco(Request $request){
        $endereco = Endereco::create($request->all());
        $usuario = Auth::user();
        $usuario->endereco = $endereco->id;
        $usuario->save();
        Flash::success('Endereço cadastrado com sucesso!');
        return redirect()->back();
    }

    public function conta_inativa(Request $request){
      return view('errors.confirm_account');
    }

    public function reenviarConfirmacao(Request $request){
        $user = Auth::user();
        if(!$user->ativo){
          $this->notify(new EmailConfirmation($user));
          Flash::success('Enviamos novamente o link com a confirmação do seu cadastro');
          return redirect('/anuncios');
        }else{
          Flash::error('A sua conta já está ativada');
          return redirect('/anuncios');
        }
    }

    public function routeNotificationForMail()
    {
        return Auth::user()->email;
    }

    public function confirmAccount(Request $request, $token){
      $user = User::where('confirm_token', $token)->firstOrFail();
      if($user->ativo){
        Flash::warning('Este usuário já foi ativado!');
        return redirect('/anuncios');
      }else{
        $user->ativo = true;
        Flash::success('Usuário ativado com sucesso! Agora você pode usufruir de todas as funcionalidades.');
        return redirect('/anuncios');
      }
    }

}
