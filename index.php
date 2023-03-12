<?php
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
error_reporting(E_ALL);

enum TipOperatie
{
    case IncepeCuArg;
    case AdunaArg;
    case ScadeArg;
    case InmultesteCuArg;
    case ImparteLaArg;

    case AdunaExpresie;
    case ScadeExpresie;
    case InmultesteCuExpresie;
    case ImparteLaExpresie;
    case TerminaExpresie;

    public function toString(): string
    {
        return match($this)
        {
            TipOperatie::IncepeCuArg => "IncepeCuArg",
            TipOperatie::AdunaArg => "AdunaArg",
            TipOperatie::ScadeArg => "ScadeArg",
            TipOperatie::InmultesteCuArg => "InmultesteCuArg",
            TipOperatie::ImparteLaArg => "ImparteLaArg",
        
            TipOperatie::AdunaExpresie => "AdunaExpresie",
            TipOperatie::ScadeExpresie => "ScadeExpresie",
            TipOperatie::InmultesteCuExpresie => "InmultesteCuExpresie",
            TipOperatie::ImparteLaExpresie => "ImparteLaExpresie",
            TipOperatie::TerminaExpresie => "TerminaExpresie"
        };
    }
}

class Operatie
{
    public int $nivelIerarhic;
    public TipOperatie $tip;
    public float $argument;

    public function __construct(int $nivelIerarhic, TipOperatie $tip, float $argument)
    {
        if($nivelIerarhic >= 0)
            $this->nivelIerarhic = $nivelIerarhic;
        else
            $this->nivelIerarhic = 0;

        $this->tip = $tip;

        if($tip === TipOperatie::ImparteLaArg && $argument === 0)
            $this->argument = 1;
        else
            $this->argument = $argument;
    }

    public function __toString(): string
    {
        $indentare = str_repeat("   ", $this->nivelIerarhic+1);
        return "{$indentare}nivel: {$this->nivelIerarhic}, tip: {$this->tip->toString()}, arg: {$this->argument}\n";
    }

    public function convertesteTipCatreOpSimpla(): void
    {

        switch($this->tip)
        {
            case TipOperatie::AdunaExpresie:
                $this->tip = TipOperatie::AdunaArg;
                break;
            case TipOperatie::ScadeExpresie:
                $this->tip = TipOperatie::ScadeArg;
                break;
            case TipOperatie::InmultesteCuExpresie:
                $this->tip = TipOperatie::InmultesteCuArg;
                break;
            case TipOperatie::ImparteLaExpresie:
                $this->tip = TipOperatie::ImparteLaArg;
                break;
        }
    }
}


class CalculeAritm
{
    private array $listaOperatii = [];
    private float $rezultat = 0;

    public function __constructor()
    {
        $this->listaOperatii = array();
    }

    public function incepeCu(float $arg): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >= 1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            // operatie de tip "IncepeCu" apare mereu la inceputul unei noi expresii
            // deci se mareste indexul ei
            $nivelOperatie = $operatiePrecedenta->nivelIerarhic + 1;
        }

        $operatieInceput = new Operatie($nivelOperatie, TipOperatie::IncepeCuArg, $arg); 
        array_push($this->listaOperatii, $operatieInceput); 

        return $this;
    }

    public function aduna(float $arg): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >= 1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            if($operatiePrecedenta->tip !== TipOperatie::TerminaExpresie)
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
            else
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic - 1;
        }

        $operatieAdunare = new Operatie($nivelOperatie, TipOperatie::AdunaArg, $arg); 
        array_push($this->listaOperatii, $operatieAdunare);

        return $this;
    }

    public function scade(float $arg): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >= 1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            if($operatiePrecedenta->tip !== TipOperatie::TerminaExpresie)
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
            else
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic - 1;
        }

        $operatieScadere = new Operatie($nivelOperatie, TipOperatie::ScadeArg, $arg); 
        array_push($this->listaOperatii, $operatieScadere);

        return $this;
    }

    public function inmultesteCu(float $arg): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >= 1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            if($operatiePrecedenta->tip !== TipOperatie::TerminaExpresie)
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
            else
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic - 1;
        }

        $operatieInmultire = new Operatie($nivelOperatie, TipOperatie::InmultesteCuArg, $arg); 
        array_push($this->listaOperatii, $operatieInmultire);

        return $this;
    }

    public function imparteLa(float $arg): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >= 1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            if($operatiePrecedenta->tip !== TipOperatie::TerminaExpresie)
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
            else
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic - 1;
        }

        if($arg != 0)
        {
            $operatieImpartire = new Operatie($nivelOperatie, TipOperatie::ImparteLaArg, $arg); 
            array_push($this->listaOperatii, $operatieImpartire);
        }
        else
            echo "eroare: imparteLa(1): argumetul este 0, impartirea la o este interzisa";

        return $this;
    }

    public function adunaExpresie(): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >=1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
        }

        $operatieAdunareExpr = new Operatie($nivelOperatie, TipOperatie::AdunaExpresie, 0); 
        array_push($this->listaOperatii, $operatieAdunareExpr);

        return $this;
    }

    public function scadeExpresie(): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >=1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
        }

        $operatieScadereExpr = new Operatie($nivelOperatie, TipOperatie::ScadeExpresie, 0); 
        array_push($this->listaOperatii, $operatieScadereExpr);

        return $this;
    }

    public function inmultesteCuExpresie(): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >=1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
        }

        $operatieInmultireCuExpr = new Operatie($nivelOperatie, TipOperatie::InmultesteCuExpresie, 0); 
        array_push($this->listaOperatii, $operatieInmultireCuExpr);

        return $this;
    }

    public function inmparteLaExpresie(): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) >=1)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
        }

        $operatieImparteLaExpr = new Operatie($nivelOperatie, TipOperatie::ImparteLaExpresie, 0); 
        array_push($this->listaOperatii, $operatieImparteLaExpr);

        return $this;
    }

    public function terminaExpresie(): CalculeAritm
    {
        $nivelOperatie = 0;

        // daca sirul nu este gol
        if(count($this->listaOperatii) > 0)
        {
            $operatiePrecedenta = end($this->listaOperatii);
            if($operatiePrecedenta->tip !== TipOperatie::TerminaExpresie)
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic;
            else
                $nivelOperatie = $operatiePrecedenta->nivelIerarhic - 1;

            $operatieTerminaExpr = new Operatie($nivelOperatie, TipOperatie::TerminaExpresie, 0); 
            array_push($this->listaOperatii, $operatieTerminaExpr);
        }
        else
            echo "eroare: terminaExpresie(): sirul este gol";

        return $this;
    }

    private function gasesteOperatiePrioritateMaxima(array $operatiiExpresie): int
    {
        $indexGasit = -1;

        foreach($operatiiExpresie as $index=>$operatie)
        {
            if($operatie->tip === TipOperatie::InmultesteCuArg or $operatie->tip === TipOperatie::ImparteLaArg)
            {
                $indexGasit = $index;
                break;
            }
        }

        // daca nu s-a gasit nici o operatie de prioritate mare (inmultire sau impartire)
        if($indexGasit == -1)
        {
            // atunci se returneaza indexul primei operatii de prioritate scazuta (adunare sau scadere)
            // indexul va fi mereu 1, adica $operatiiExpresie[1] deoarece prima operatie dintr-o expresie
            // este doar un numar, deci de abia a 2-a operatie este o operatie adevarata
            foreach($operatiiExpresie as $index=>$operatie)
            {
                if($operatie->tip === TipOperatie::AdunaArg or $operatie->tip === TipOperatie::ScadeArg)
                {
                    $indexGasit = $index;
                    break;
                }
            }
        }
        return $indexGasit;
    }

    private function gasesteExpresieNivelMaxim(int &$indexInceput, int &$indexSfarsit): array
    {
        $nivelMaxGasit = -1;
        $listaOpExpresieNivelMax = [];

        // prima data se gaseste indexul de inceput al expresiei de nivel maxim
        foreach($this->listaOperatii as $index=>$operatie)
        {
            // daca operatia curenta are un nivel ierarhic mai mare decat cel gasit pana acum,
            // atunci se stocheaza nivelul ei si indexul la care s-a gasit
            if($operatie->tip === TipOperatie::IncepeCuArg && $operatie->nivelIerarhic > $nivelMaxGasit)
            {
                $nivelMaxGasit = $operatie->nivelIerarhic;
                $indexInceput = $index;
            }
        }

        // apoi se gaseste indexul de sfarsit al expresiei de nivel minim
        $nivelCurent = 0;
        $numarOperatii = count($this->listaOperatii);
        for($i=$indexInceput; $i<$numarOperatii; $i++)
        {
            $nivelCurent = $this->listaOperatii[$i]->nivelIerarhic;
            // echo "i={$i}: indexSfarsit: {$indexSfarsit}\n";
            if($this->listaOperatii[$i]->tip === TipOperatie::TerminaExpresie && $nivelCurent == $nivelMaxGasit)
            {
                $indexSfarsit = $i;
                break;
            }
        }

        // daca nu s-a gasit nimic, atunci inseamna ca nu mai sunt expresii complexe, ci a mai ramas doar
        // o singura expresie simpla de calculat, care dureaza pana la sfarsitul sirului de operatii
        if($indexSfarsit === -1)
            $indexSfarsit = count($this->listaOperatii) - 1;

        $listaOpExpresieNivelMax = array_slice($this->listaOperatii, $indexInceput, $indexSfarsit-$indexInceput+1);

        return $listaOpExpresieNivelMax;
    }

    private function calculeazaOperatieSimpla(array $operatiiExpresie, int $indexOperatieSimpla): array
    {
        // echo "calculeazaOperatieSimpla(): \n";
        $rezultat = 0.0;

        if(count($operatiiExpresie) >= 2)
        {
            // daca mai exista macar o operatie precedenta celei curente
            if(0<$indexOperatieSimpla and $indexOperatieSimpla<count($operatiiExpresie))
            {
                $operatieCurenta = $operatiiExpresie[$indexOperatieSimpla];
                $operatiePrecedenta = $operatiiExpresie[$indexOperatieSimpla - 1];

                switch($operatieCurenta->tip)
                {
                    case TipOperatie::AdunaArg:
                        $rezultat = $operatiePrecedenta->argument + $operatieCurenta->argument;
                        break;
                    case TipOperatie::ScadeArg:
                        $rezultat = $operatiePrecedenta->argument - $operatieCurenta->argument;
                        break;
                    case TipOperatie::InmultesteCuArg:
                        $rezultat = $operatiePrecedenta->argument * $operatieCurenta->argument;
                        break;
                    case TipOperatie::ImparteLaArg:
                        $rezultat = $operatiePrecedenta->argument / $operatieCurenta->argument;
                        break;
                    default:
                        echo "eroare: calculeazaOperatieSimpla(2): operatia nu este simpla\n";
                        break;
                }
                // op. curenta a fost efectuata deci nu ar mai trebui sa exista si va fi eliminata din sir
                // astfel ca, se "paseaza" rezultatul mai departe la op. precedenta
                $operatiePrecedenta->argument = $rezultat;

                // se recreaza sirul de operatii pasat acestei functii, a.i sa aiba op. curenta eliminata
                array_splice($operatiiExpresie, $indexOperatieSimpla, 1);
            }
        }

        return $operatiiExpresie;
    }

    private function finalizeazaExpresie(int $indexInceput, int $indexSfarsit, array $listaOperatiiCurente): void
    {
        // daca expresia curenta nu este singura ramasa
        if($indexInceput !== 0)
        {
            $operatiePrecedenta = $this->listaOperatii[$indexInceput - 1];
            $operatieInceputExpresie = $listaOperatiiCurente[0];
    
            $operatiePrecedenta->convertesteTipCatreOpSimpla();
            $operatiePrecedenta->argument = $operatieInceputExpresie->argument;
            array_splice($this->listaOperatii, $indexInceput, $indexSfarsit-$indexInceput+1);
        }
        else
            array_splice($this->listaOperatii, $indexInceput+1, $indexSfarsit);

    }

    private function calculeazaExpresieNivelMaxim(): float
    {
        $rezultat = 0.0;
        $indexInceput = -1;
        $indexSfarsit = -1;
        $listaOperatiiCurente = [];
        $indexOperatieCurenta = -1;
        $numarOperatiiCurente = -1;

        $listaOperatiiCurente = $this->gasesteExpresieNivelMaxim($indexInceput, $indexSfarsit);
        $numarOperatiiCurente = count($listaOperatiiCurente);

        $i = 0;
        while(count($listaOperatiiCurente) > 1 and $i < $numarOperatiiCurente)
        {
            $indexOperatieCurenta = $this->gasesteOperatiePrioritateMaxima($listaOperatiiCurente);
            $listaOperatiiCurente = $this->calculeazaOperatieSimpla($listaOperatiiCurente, $indexOperatieCurenta);
            $i++;
        }

        $this->finalizeazaExpresie($indexInceput, $indexSfarsit, $listaOperatiiCurente);

        return $rezultat;
    }

    public function calculeaza(): float
    {
        $rezultat = 0.0;

        $numarInitialOperatii = count($this->listaOperatii);
        $i = 0;
        while(count($this->listaOperatii) > 1 and $i < $numarInitialOperatii)
        {
            echo "i={$i}: \n";
            $this->logheazaOperatii();
            $this->calculeazaExpresieNivelMaxim();
            $i++;
        }

        $rezultat = $this->listaOperatii[0]->argument;
        echo "rezultatul este: {$rezultat}";

        return $rezultat;
    }

    public function logheazaOperatii(): void
    {
        foreach($this->listaOperatii as $operatie)
        {
            echo $operatie;
        }
        echo "\n";
    }

    public function logheazaOperatiiIntre(int $indexInceput, int $indexSfarsit): void
    {
        for($i=$indexInceput; $i<=$indexSfarsit; $i++)
        {
            echo $this->listaOperatii[$i];
        }
        echo "\n";
    }

    public function logheazaSirOperatii(array $listaOperatii): void
    {
        foreach($listaOperatii as $operatie)
        {
            echo $operatie;
        }
        echo "\n";
    }
}

$calcule = new CalculeAritm();

/* cum functioneaza:
- fiecare expresie trebuie sa inceapa cu metoda "incepeCu()", de exemplu 2+4-5 va fi:
    $calcule->incepeCu(2)
            ->aduna(4)
            ->scade(5)
- daca se doreste sa se adauge o paranteza (adica o expresie nu un numar simplu), atunci se folosesc metodele
 de operatii cu expresii: adunaExpresie(), scadeExpresie(), inmultesteCuExpresie(), inmparteLaExpresie().
 Dupa ce s-a inceput o expresie, prima metoda trebui sa fie obligatoriu "incepeCu()", ca expresia sa aiba primul
 termen. Expresia se inchide cu metoda terminaExpresie() - este ca si cum "s-ar inchide paranteza".
 Daca o expresie nu se termina cu metoda terminaExpresie(), rezultatul nu va fi corect sau vor aparea bug-uri.
 Exemplu: 2-5*(2+3/4) s-ar scrie:
 $calcule->incepeCu(2)
        ->scade(5)
        ->inmultesteCuExpresie() // de aici incepe expresia
            ->incepeCu(2)
            ->aduna(3)
            ->imparteLa(4)
            ->terminaExpresie() // aici se termina expresia
- expresiile se pot include una intr-alta, oricat de mult (ierarhia poate fi oricat de complexa)
- sunt disponibile doar cele 4 operatii aritmetice: adunare, scadere, inmultire, impartire
- metode pt. argumente simple (numere):
    aduna($numar)
    scade($numar)
    inmultesteCu($numar)
    imparteLa($numar)
- metode pt. a incepe o expresie:
    adunaExpresie()
    scadeExpresie()
    inmultesteCu(Expresie)
    imparteLaExpresie()
- calculul expresiei se face doar daca se apeleaza metoda calculeaza()
*/

// exemplu de utilizare pt. operatia: 8 + 2 - 3*(4 - 2*5 -(10/4 - 8 + 20)/3) -10/5 + (4 + 5/4):
$calcule->incepeCu(8)
        ->aduna(2)
        ->scade(3)
        ->inmultesteCuExpresie()
            ->incepeCu(4)
            ->scade(2)
            ->inmultesteCu(5)
            ->scadeExpresie()
                ->incepeCu(10)
                ->imparteLa(4)
                ->scade(8)
                ->aduna(20)
                ->terminaExpresie()
            ->imparteLa(3)
            ->terminaExpresie()
        ->scade(10)
        ->imparteLa(5)
        ->adunaExpresie()
            ->incepeCu(4)
            ->aduna(5)
            ->imparteLa(4)
            ->terminaExpresie();
?>

<pre><?php $calcule->calculeaza(); // la sfarsit se poate calcula expresia ?></pre>
